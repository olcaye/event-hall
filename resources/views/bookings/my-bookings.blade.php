@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">My Bookings</h2>

    <ul class="nav nav-tabs mb-3" id="myBookingsTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="active-bookings-tab" data-bs-toggle="tab" data-bs-target="#active-bookings-pane" type="button" role="tab" aria-controls="active-bookings-pane" aria-selected="true">Active Bookings</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="past-bookings-tab" data-bs-toggle="tab" data-bs-target="#past-bookings-pane" type="button" role="tab" aria-controls="past-bookings-pane" aria-selected="false">Past & Other Bookings</button>
        </li>
    </ul>

    <div class="tab-content" id="myBookingsTabContent">
        {{-- Active Bookings Tab Pane --}}
        <div class="tab-pane fade show active" id="active-bookings-pane" role="tabpanel" aria-labelledby="active-bookings-tab" tabindex="0">
            @if($activeBookings->count())
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Event</th>
                                <th>Location</th>
                                <th>Date</th>
                                <th>Starts At</th>
                                <th>Status</th>
                                <th>Details</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($activeBookings as $booking)
                                <tr>
                                    <td>{{ $booking->event->title }}</td>
                                    <td>{{ $booking->event->location }}</td>
                                    <td>{{ $booking->event->formattedDate }}</td>
                                    <td>{{ $booking->event->start_time->format('h:i A') }}</td>
                                    <td>
                                        <span class="badge bg-success">{{ $booking->event->status->value }}</span>
                                    </td>
                                    <td>
                                        <a href="{{ route('events.show', $booking->event) }}" class="btn btn-sm btn-primary">
                                            View
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                {{-- Pagination for Active Bookings --}}
                <div class="d-flex justify-content-center">
                    {{ $activeBookings->links() }}
                </div>
            @else
                <p class="text-muted">You have no active event bookings.</p>
            @endif
        </div>

        {{-- Past & Other Bookings Tab Pane --}}
        <div class="tab-pane fade" id="past-bookings-pane" role="tabpanel" aria-labelledby="past-bookings-tab" tabindex="0">
            @if($pastBookings->count())
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Event</th>
                                <th>Location</th>
                                <th>Date</th>
                                <th>Starts At</th>
                                <th>Status</th>
                                <th>Details</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pastBookings as $booking)
                                <tr>
                                    <td>{{ $booking->event->title }}</td>
                                    <td>{{ $booking->event->location }}</td>
                                    <td>{{ $booking->event->formattedDate }}</td>
                                    <td>{{ $booking->event->start_time->format('h:i A') }}</td>
                                    <td>
                                        @if($booking->event->status === \App\Enums\EventStatus::ENDED)
                                            <span class="badge bg-secondary">{{ $booking->event->status->value }}</span>
                                        @elseif($booking->event->status === \App\Enums\EventStatus::CANCELLED)
                                            <span class="badge bg-danger">{{ $booking->event->status->value }}</span>
                                        @else
                                            <span class="badge bg-warning text-dark">{{ $booking->event->status->value }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('events.show', $booking->event) }}" class="btn btn-sm btn-primary">
                                            View
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                {{-- Pagination for Past Bookings --}}
                <div class="d-flex justify-content-center">
                    {{ $pastBookings->links() }}
                </div>
            @else
                <p class="text-muted">You have no past or other event bookings.</p>
            @endif
        </div>
    </div>
</div>
@endsection
