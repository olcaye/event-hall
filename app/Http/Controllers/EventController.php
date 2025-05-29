<?php

namespace App\Http\Controllers;

use App\Enums\EventStatus;
use App\Http\Requests\EventRequest;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $events         = Event::where('status', EventStatus::ACTIVE)->latest()->paginate(20);
        $bookedEventIds = auth()->check() ? auth()->user()->bookings()->pluck('event_id')->toArray() : [];
        return view('events.index', compact('events', 'bookedEventIds'));
    }

    public function attendees(Event $event)
    {
        $attendees = $event->bookings()->with('user')->get()->map(function ($booking) {
            return [
                'name'  => $booking->user->name,
                'email' => $booking->user->email,
            ];
        });

        return response()->json($attendees);
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('events.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(EventRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('cover_image')) {
            $data['cover_image'] = $request->file('cover_image')->store('events/covers', 'public');
        }

        $galleryImages = [];
        if ($request->hasFile('gallery')) {
            foreach ($request->file('gallery') as $image) {
                $path            = $image->store('events/gallery', 'public');
                $galleryImages[] = $path;
            }
        }
        $data['gallery'] = $galleryImages;


        $data['user_id'] = Auth::id();

        Event::create($data);

        return redirect()
            ->route('my-events')
            ->with('success', 'Event created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Event $event)
    {
        $event->load(['bookings.user', 'user']);
        $hostedEventsCount = $event->user->events()->count();

        $isBooked = auth()->check() && $event->bookings()->where('user_id', auth()->id())->exists();
        $daysLeft = now()->diffInDays($event->date);

        return view('events.show', compact('event', 'hostedEventsCount', 'isBooked', 'daysLeft'));
    }

    public function myEvents()
    {
        $events = auth()->user()
            ->events()
            ->withCount('bookings')
            ->latest()
            ->get();

        return view('events.my-events', compact('events'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Event $event)
    {
        $this->authorize('update', $event);
        return view('events.edit', compact('event'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(EventRequest $request, Event $event)
    {
        $this->authorize('update', $event);
        $data = $request->validated();

        if ($request->hasFile('cover_image')) {
            if ($event->cover_image && Storage::disk('public')->exists($event->cover_image)) {
                Storage::disk('public')->delete($event->cover_image);
            }
            $coverImagePath      = $request->file('cover_image')->store('events/cover', 'public');
            $data['cover_image'] = $coverImagePath;
        }

        $event->update($data);

        return redirect()
            ->route('my-events')
            ->with('success', 'Event updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Event $event)
    {
        $this->authorize('delete', $event);

        if ($event->bookings()->count() > 0) {
            return back()->with('error', 'You can not delete an event with bookings.');
        }

        $event->delete();

        return redirect()->route('my-events')->with('success', 'Event deleted.');
    }

    public function uploadImage(Request $request, Event $event)
    {
        $request->validate([
            'file' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
        ]);

        $image    = $request->file('file');
        $filepath = $image->store('events/gallery', 'public');

        $gallery   = $event->gallery ?? [];
        $gallery[] = $filepath;

        $event->update(['gallery' => $gallery]);

        return response()->json(['success' => true, 'filepath' => $filepath, 'url' => Storage::url($filepath)]);
    }

    public function deleteImage(Request $request, Event $event)
    {
        $request->validate([
            'filepath' => 'required|string',
        ]);

        $filepathToDelete = $request->filepath;

        $gallery = $event->gallery ?? [];

        $updatedGallery = array_values(array_diff($gallery, [$filepathToDelete]));

        if (Storage::disk('public')->exists($filepathToDelete)) {
            Storage::disk('public')->delete($filepathToDelete);
        }

        $event->update(['gallery' => $updatedGallery]);

        return response()->json(['success' => true]);
    }
}
