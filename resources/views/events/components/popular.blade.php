<div class="container mb-5">
        <div class="d-flex justify-content-between align-items-baseline">
            <h2 class="mb-4" style="font-weight: 700">Popular Events</h2>
        </div>

        @if($popularEvents->count())
            <div class="event-grid">
                @foreach($popularEvents as $event)
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

        @else
            <p class="text-muted">No events yet.</p>
        @endif

    </div>
