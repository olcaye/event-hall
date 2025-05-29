@extends('layouts.app')

@section('content')

    <div @class([
            'content-wrapper',
            'position-relative',
            'banner-spacer' => !$event->isActive(),
        ])>
        @if(!$event->isActive())
            <div class="container-fluid mb-4" id="event-status-banner">
                <div class="row">
                    <div id="event-is-ended" class="d-flex justify-content-center align-items-center text-white">
                        @if($event->isEnded())
                            This event has already ended. Participation is no longer possible.
                        @elseif($event->isCancelled())
                            This event has been cancelled. Participation is not possible.
                        @elseif($event->isSuspended())
                            This event is currently suspended. Participation is temporarily unavailable.
                        @elseif($event->isPending())
                            This event is awaiting approval and is not yet open for participation.
                        @else
                            Participation in this event is currently not possible.
                        @endif
                    </div>
                </div>
            </div>
        @endif

        <div class="container mb-4">
            <div class="swiper mySwiper">
                <div class="swiper-wrapper">
                    @foreach($event->gallery ?? [] as $image)
                        <div class="swiper-slide">
                            <div class="event-bg" style="background-image: url({{ asset('storage/' . $image) }})"></div>
                            <div class="event-img">
                                <img src="{{ asset('storage/' . $image) }}" alt="Event Image">
                            </div>

                        </div>
                    @endforeach
                </div>
                <div class="swiper-pagination"></div>
            </div>
        </div>

        <div class="container" id="event-details">
            <div class="row">
                <div class="col-md-7">

                    <p class="event-date">
                        {{ $event->date->format('l, F d') }}
                    </p>

                    <h2 class="mb-4">
                        {{ $event->title }}
                    </h2>

                    <p>
                        {{ $event->description }}
                    </p>
                    <div class="event-details__section my-5">
                        <div class="event-host-card d-flex align-items-center mt-4">
                            <img
                                src="https://media.licdn.com/dms/image/v2/C4E03AQH7mN-f2wPBzw/profile-displayphoto-shrink_200_200/profile-displayphoto-shrink_200_200/0/1625252765091?e=1753920000&v=beta&t=nMPChQhiaGtEizPNVCWeE_m1inINWjnN4Nh1dJCEDnA"
                                alt="User avatar"
                                class="rounded-circle me-3"
                                width="48" height="48">

                            <div class="d-flex flex-row align-items-start justify-content-between w-100">
                                <div class="d-flex flex-column">
                                    <span>Hosted By</span>
                                    <strong>{{ $event->user->name }}</strong>
                                </div>
                                @php
                                    $formattedCount = $hostedEventsCount > 999
                                        ? number_format($hostedEventsCount / 1000, 1) . 'k'
                                        : $hostedEventsCount;
                                @endphp
                                <span class="event-badge">{{ $formattedCount }} events hosted</span>
                            </div>

                        </div>
                    </div>

                    <div class="event-details__section mt-5">
                        <div class="event-details__section-title mb-4">
                            <h2 id="location-heading">Location</h2>
                        </div>

                        <div class="mb-3 d-flex align-items-start">
                            <i class="bi bi-geo-alt-fill me-2 fs-5 text-primary"></i>
                            <div>
                                <strong>Meeting Point:</strong> {{ $event->location }}
                            </div>
                        </div>

                        <div class="mb-3 d-flex align-items-start">
                            <i class="bi bi-calendar-event me-2 fs-5 text-primary"></i>
                            <div>
                                <strong>Date:</strong> {{ $event->date->format('d M Y') }}
                            </div>
                        </div>

                        <div class="mb-3 d-flex align-items-start">
                            <i class="bi bi-clock me-2 fs-5 text-primary"></i>
                            <div>
                                <strong>Event Time:</strong> {{ $event->start_time->format('H:i') }}
                                - {{ $event->end_time->format('H:i') }}
                            </div>
                        </div>

                        <div id="map" class="w-100" style="height: 300px; margin-top: 20px; border-radius: 10px;"></div>

                        @php
                            $lat = $event->latitude;
                            $lng = $event->longitude;
                            $mapsBase = "https://www.google.com/maps/dir/?api=1&destination={$lat},{$lng}&travelmode=";
                        @endphp
                        <div class="mt-3 text-center">
                            <h5 class="mb-3" style="font-weight: 600;">How to get there</h5>
                            <div class="d-flex justify-content-center align-items-center gap-4">

                                <a href="{{ $mapsBase }}driving" target="_blank" title="By car">
                                    <i class="bi bi-car-front" style="font-size: 24px; color: #0d6efd;"></i>
                                </a>

                                <a href="{{ $mapsBase }}walking" target="_blank" title="On foot">
                                    <i class="bi bi-person-walking" style="font-size: 24px; color: #0d6efd;"></i>
                                </a>

                                <a href="{{ $mapsBase }}transit" target="_blank" title="By public transport">
                                    <i class="bi bi-bus-front" style="font-size: 24px; color: #0d6efd;"></i>
                                </a>

                                <a href="{{ $mapsBase }}bicycling" target="_blank" title="By bike">
                                    <i class="bi bi-bicycle" style="font-size: 24px; color: #0d6efd;"></i>
                                </a>

                            </div>
                        </div>
                    </div>

                </div>
                @if($event->isActive())
                    <div class="col-md-5" id="booking-area">
                        <div class="booking-wrapper">
                            @if(isset($daysLeft) && $daysLeft >= 0)
                                <div @class([
                                    'text-center',
                                    'fw-bold',
                                    'mb-3' => auth()->check() && auth()->id() !== $event->user_id,
                                ])>
                                    @if($isBooked)
                                        @php
                                            $dayCount = (int) $daysLeft;
                                            $msg = match(true) {
                                                $dayCount > 1  => "You're in! Only $dayCount days left. Get ready!",
                                                $dayCount === 1 => "It's tomorrow! Hope you're excited!",
                                                $dayCount === 0 => "Today's the day! See you there!",
                                            };
                                        @endphp
                                        {{ $msg }}
                                    @else
                                        @php
                                            $dayCount = (int) $daysLeft;
                                            $msg = match(true) {
                                                $dayCount > 1  => "Only $dayCount days left to join!",
                                                $dayCount === 1 => "Last chance! It's tomorrow!",
                                                $dayCount === 0 => "Final call! It's today!",
                                            };
                                        @endphp
                                        {{ $msg }}
                                    @endif
                                </div>
                            @endif

                            @if(auth()->check() && auth()->id() !== $event->user_id)
                                @if($isBooked)
                                    <button class="btn btn-danger w-100 py-2" id="cancelBookingBtn">Cancel Booking
                                    </button>
                                @else
                                    <button class="btn btn-success  w-100 py-2" id="bookEventBtn">Book this event
                                    </button>
                                @endif
                            @elseif(auth()->guest())
                                <a href="{{ route('login') }}" class="btn btn-primary w-100">Login to book this
                                    event</a>
                            @endif
                        </div>

                    </div>
                @endif
            </div>


            @php
                $visibleUsers = $event->bookings->take(5);
                $extraCount = $event->bookings->count() - $visibleUsers->count();
            @endphp

            @if(auth()->check())
                <hr>

                <h4>Booked Users ({{ $event->bookings->count() }})</h4>

                @if($event->bookings->count())
                    <div class="d-flex align-items-start mb-3">
                        @foreach($visibleUsers as $booking)
                            <div class="me-3 text-center">
                                <img src="{{ $booking->user->avatar_url }}" alt="{{ $booking->user->name }}"
                                     class="rounded-circle mb-1" style="width: 48px; height: 48px;">
                                <div style="font-size: 12px; font-weight: 500;">
                                    {{ $booking->user->display_name }}
                                </div>
                            </div>
                        @endforeach

                        @if($extraCount > 0)
                            <div class="position-relative me-2 text-center">
                                <div
                                    class="rounded-circle bg-secondary text-white d-flex justify-content-center align-items-center"
                                    style="width: 48px; height: 48px; font-size: 14px;cursor: pointer"
                                    data-bs-toggle="modal"
                                    data-bs-target="#userListModal">
                                    +{{ $extraCount }}
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="modal fade" id="userListModal" tabindex="-1" aria-labelledby="userListModalLabel"
                         aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">All Booked Users</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <ul class="list-group">
                                        @foreach($event->bookings as $booking)

                                            <li class="list-group-item">
                                                @if(auth()->check() && auth()->id() !== $event->user_id)
                                                    {{ $booking->user->display_name }}
                                                @else
                                                    {{ $booking->user->name }}
                                                @endif

                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <p class="text-muted">No one has booked this event yet.</p>
                @endif
            @endif


        </div>
    </div>

@endsection


@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css"/>
@endpush


@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#bookEventBtn').on('click', function () {
                Swal.fire({
                    title: 'Are you sure?',
                    text: "Do you want to book this event?",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, book it!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('bookings.store', $event) }}",
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
                                const errorMsg = xhr.responseJSON?.message || 'An unexpected error occurred.';
                                Swal.fire('Oops', errorMsg, 'error');
                            }
                        });
                    }
                });
            });

            $('#cancelBookingBtn').on('click', function () {
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
                            url: "{{ route('bookings.cancel', $event) }}",
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

            var swiper = new Swiper(".mySwiper", {
                pagination: {
                    el: ".swiper-pagination",
                },
            });
        });

        function initMap() {
            const lat = parseFloat('{{ $event->latitude }}');
            const lng = parseFloat('{{ $event->longitude }}');
            const position = {lat: lat, lng: lng};

            const map = new google.maps.Map(document.getElementById("map"), {
                center: position,
                zoom: 16,
                mapId: "DEMO_MAP_ID"
            });

            const {AdvancedMarkerElement} = google.maps.marker;

            const marker = new AdvancedMarkerElement({
                map: map,
                position: position,
                title: "{{ $event->title }}",
            });
        }
    </script>

    <script
        src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.key') }}&callback=initMap&libraries=marker&v=weekly"
        async defer>
    </script>

@endpush
