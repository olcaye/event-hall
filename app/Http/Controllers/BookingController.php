<?php

namespace App\Http\Controllers;

use App\Enums\EventStatus;
use App\Models\Booking;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class BookingController extends Controller
{
    public function store(Request $request, Event $event)
    {
        $user = auth()->user();

        $response = Gate::inspect('create', [Booking::class, $event]);

        if (!$response->allowed()) {
            return response()->json([
                'success' => false,
                'message' => $response->message() ?? 'Unauthorized.',
            ], 403);
        }

        if (!is_null($event->booking_limit) && $event->bookings()->count() >= $event->booking_limit) {
            return response()->json([
                'success' => false,
                'message' => 'Booking limit has been reached.',
            ], 422);
        }

        $existing = Booking::withTrashed()
            ->where('event_id', $event->id)
            ->where('user_id', $user->id)
            ->first();

        if ($existing) {
            if ($existing->trashed()) {
                $existing->restore();

                return response()->json([
                    'success' => true,
                    'message' => 'Your booking has been restored.',
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'You have already booked this event.',
                ], 409);
            }
        }

        Booking::create([
            'event_id' => $event->id,
            'user_id'  => $user->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Successfully booked the event.',
        ]);
    }

    public function cancel(Event $event)
    {
        $booking = $event->bookings()->where('user_id', auth()->id())->first();

        if (!$booking) {
            return response()->json(['message' => 'You have not booked this event.'], 404);
        }

        $booking->delete();

        return response()->json(['message' => 'Your booking has been successfully cancelled.']);
    }


    /*    public function myBookings()
        {
            $bookings = Booking::with('event')
                ->where('user_id', auth()->id())
                ->latest()
                ->get();

            return view('bookings.my-bookings', compact('bookings'));
        }*/

    public function myBookings()
    {
        $userId  = Auth::id();
        $perPage = 10;

        $activeBookings = Booking::with([
            'event' => function ($query) {
                $query->orderBy('date', 'asc')->orderBy('start_time', 'asc');
            }
        ])
            ->where('bookings.user_id', $userId)
            ->join('events', 'bookings.event_id', '=', 'events.id')
            ->where('events.status', EventStatus::ACTIVE)
            ->select('bookings.*')
            ->orderBy('events.date')
            ->orderBy('events.start_time')
            ->paginate($perPage, ['*'], 'active_page');

        $pastBookings = Booking::with([
            'event' => function ($query) {
                $query->orderBy('date', 'desc')->orderBy('start_time', 'desc');
            }
        ])
            ->where('bookings.user_id', $userId)
            ->join('events', 'bookings.event_id', '=', 'events.id')
            ->where('events.status', '!=', EventStatus::ACTIVE)
            ->select('bookings.*')
            ->orderBy('events.date', 'desc')
            ->orderBy('events.start_time', 'desc')
            ->paginate($perPage, ['*'], 'past_page');

        return view('bookings.my-bookings', compact('activeBookings', 'pastBookings'));
    }

}
