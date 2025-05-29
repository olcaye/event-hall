@extends('layouts.app')


@section('content')
    <div class="container">
        <div class="d-flex justify-content-between align-items-baseline">
            <h2 class="mb-4" style="font-weight: 700">On Going Events</h2>
        </div>


        @if($events->count())
            <div class="event-grid">
                @foreach($events as $event)
                    <article class="event-card position-relative">
                    <a href="{{ route('events.show', $event) }}" class="event-card-link">
                        <div class="event-card-image">
                              <img src="{{ asset('storage/' . $event->cover_image) }}" alt="{{ $event->title }}">
                        </div>
                        <div @class([
                                    'event-card-content',
                                    'booking-spacer' => auth()->check(),
                                ]) >
                            <h3 class="event-card-title">{{ $event->title }}</h3>
                            <div class="d-flex justify-content-between align-items-baseline">
                                <div class="d-flex flex-column" style="min-width: 0;">
                                    <span class="event-card-location">{{ $event->location }}</span>
                                    <span class="event-card-time">{{ $event->date->format('D, M j') }}
                                            • {{ $event->start_time->format('h:i A') }} </span>
                                </div>

                            </div>

                        </div>
                    </a>
                    <div class="position-absolute booking-card-action-area">
                        @if(auth()->check() && auth()->id() !== $event->user_id && !in_array($event->id, $bookedEventIds))
                                <button
                                    class="btn quick-booking-btn"
                                    data-event-id="{{ $event->id }}"
                                    data-event-title="{{ $event->title }}"
                                    data-booking-url="{{ route('bookings.store', $event) }}"
                                    title="Quick Book">
                                    Quick Booking
                                </button>
                        @endif
                    </div>

                </article>
                @endforeach
            </div>
            <div class="mt-4 d-flex justify-content-center">
                {{ $events->links() }}
            </div>
        @else
            <p class="text-muted">No events yet.</p>
        @endif

    </div>
@endsection

@push('scripts')

    <script type="module">
        $(document).ready(function () {
            $('.quick-booking-btn').on('click', function (e) {
                e.preventDefault();
                e.stopPropagation();

                let button = $(this);

                let eventTitle = button.data('event-title');
                let bookingUrl = button.data('booking-url');

                if (!bookingUrl) {
                    Swal.fire('Oops...', 'Could not find the booking URL.', 'error');
                    return;
                }

                Swal.fire({
                    title: 'Are you sure?',
                    text: "Do you want to book '" + eventTitle + "'?",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, book it!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: bookingUrl,
                            method: "POST",
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            },
                            dataType: 'json',
                            success: function (response) {
                                Swal.fire('Booked!', response.message, 'success').then(() => {
                                    location.reload();
                                });
                            },
                            error: function (xhr) {
                                const errorMsg = (xhr.responseJSON && xhr.responseJSON.message)
                                    ? xhr.responseJSON.message
                                    : 'An unexpected error occurred. You might already be booked or the event is full.';
                                Swal.fire('Oops...', errorMsg, 'error');
                            }
                        });
                    }
                });
            });
        });
    </script>
@endpush
