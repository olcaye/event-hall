@extends('layouts.app')

@section('content')
    <div class="container">
        <h2 class="mb-4">My Bookings</h2>

        <ul class="nav nav-tabs mb-3" id="myBookingsTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="active-bookings-tab" data-bs-toggle="tab"
                        data-bs-target="#active-bookings-pane" type="button" role="tab"
                        aria-controls="active-bookings-pane" aria-selected="true">Active Bookings
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="past-bookings-tab" data-bs-toggle="tab"
                        data-bs-target="#past-bookings-pane" type="button" role="tab" aria-controls="past-bookings-pane"
                        aria-selected="false">Past & Other Bookings
                </button>
            </li>
        </ul>

        <div class="tab-content" id="myBookingsTabContent">

            <div class="tab-pane fade show active" id="active-bookings-pane" role="tabpanel"
                 aria-labelledby="active-bookings-tab" tabindex="0">

                @if($activeBookings->count())
                    <div class="event-grid mb-5">
                        @foreach($activeBookings as $booking)
                            <article class="event-card position-relative">
                                <a href="{{ route('events.show', $booking->event) }}" class="event-card-link">
                                    <div class="event-card-image">
                                        <img src="{{ asset('storage/' . $booking->event->cover_image) }}"
                                             alt="{{ $booking->event->title }}">
                                    </div>
                                    <div @class([
                                    'event-card-content',
                                    'booking-spacer' => auth()->check(),
                                ]) >
                                        <h3 class="event-card-title">{{ $booking->event->title }}</h3>
                                        <div class="d-flex justify-content-between align-items-baseline">
                                            <div class="d-flex flex-column" style="min-width: 0;">
                                                <span class="event-card-location">{{ $booking->event->location }}</span>
                                                <span class="event-card-time">{{ $booking->event->date->format('D, M j') }}
                                            • {{ $booking->event->start_time->format('h:i A') }} </span>
                                            </div>

                                        </div>

                                    </div>
                                </a>
                                <div class="position-absolute booking-card-action-area">
                                    <button
                                        class="btn cancel-booking-btn w-100"
                                        data-event-id="{{ $booking->event->id }}"
                                        data-cancel-url="{{ route('bookings.cancel', $booking->event) }}"
                                        title="Cancel Booking">
                                        Cancel Booking
                                    </button>
                                </div>

                            </article>
                        @endforeach
                    </div>


                    <div class="d-flex justify-content-center">
                        {{ $activeBookings->links() }}
                    </div>
                @else
                    <p class="text-muted">You have no active event bookings.</p>
                @endif
            </div>

            <div class="tab-pane fade" id="past-bookings-pane" role="tabpanel" aria-labelledby="past-bookings-tab"
                 tabindex="0">
                @if($pastBookings->count())
                    <div class="event-grid mb-5">
                        @foreach($pastBookings as $booking)
                            <article class="event-card position-relative">
                                <a href="{{ route('events.show', $booking->event) }}" class="event-card-link">
                                    <div class="event-card-image">
                                        <img src="{{ asset('storage/' . $booking->event->cover_image) }}"
                                             alt="{{ $booking->event->title }}">
                                    </div>
                                    <div class="event-card-content">
                                        <h3 class="event-card-title">{{ $booking->event->title }}</h3>
                                        <div class="d-flex justify-content-between align-items-baseline">
                                            <div class="d-flex flex-column" style="min-width: 0;">
                                                <span class="event-card-location">{{ $booking->event->location }}</span>
                                                <span class="event-card-time">{{ $booking->event->formattedDate }}
                                        • {{ $booking->event->start_time->format('h:i A') }}</span>
                                            </div>
                                            <span class="badge
                                    @if($booking->event->status === \App\Enums\EventStatus::ENDED)
                                        bg-secondary
                                    @elseif($booking->event->status === \App\Enums\EventStatus::CANCELLED)
                                        bg-danger
                                    @else
                                        bg-warning text-dark
                                    @endif">
                                    {{ $booking->event->status->value }}
                                </span>
                                        </div>
                                    </div>
                                </a>
                            </article>
                        @endforeach
                    </div>

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

@push('scripts')
    <script type="module">
        $(document).ready(function () {
            $('.cancel-booking-btn').on('click', function () {
                const cancelUrl = $(this).data('cancel-url');

                Swal.fire({
                    title: 'Are you sure?',
                    text: "You are about to cancel your booking.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, cancel it!',
                    cancelButtonText: 'Go back'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: cancelUrl,
                            method: "DELETE",
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            },
                            success: function (response) {
                                Swal.fire('Cancelled!', response.message, 'success').then(() => {
                                    location.reload();
                                });
                            },
                            error: function (xhr) {
                                const errorMsg = xhr.responseJSON?.message || 'An unexpected error occurred.';
                                Swal.fire('Oops', errorMsg, 'error');
                            }
                        });
                    }
                });
            });
        });
    </script>
@endpush
