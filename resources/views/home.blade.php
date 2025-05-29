@extends('layouts.app')

@section('content')
    @include('events.components.latest')
    @include('events.components.nearby')
    @include('events.components.popular')
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

        $(document).ready(function () {
            function saveUserLocation(latitude, longitude) {
                const csrfToken = $('meta[name="csrf-token"]').attr('content');

                $.ajax({
                    url: "{{ route('location.store') }}",
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    contentType: 'application/json',
                    data: JSON.stringify({
                        latitude: latitude,
                        longitude: longitude
                    }),
                    dataType: 'json',
                    success: function (data) {
                        if (data.status === 'success') {
                            window.location.reload();
                        } else {
                            console.warn(data.message);
                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        console.error('Error:', textStatus, errorThrown);
                    }
                });
            }


            @if (!session()->has('user_latitude'))
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    function (position) {
                        saveUserLocation(position.coords.latitude, position.coords.longitude);
                    },
                    function (error) {
                        console.warn(`Geolocation Error (${error.code}): ${error.message}`);
                    }
                );
            } else {
                console.log("Geolocation is not supported by this browser.");
            }
            @endif


        });
    </script>
@endpush
