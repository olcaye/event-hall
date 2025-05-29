@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>My Events</h2>
            <a href="{{ route('events.create') }}" class="btn btn-success">Create New Event</a>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if($events->count())
            <div class="">
                <table class="table table-bordered table-hover align-middle">
                    <thead class="table-light">
                    <tr>
                        <th>Title</th>
                        <th>Date</th>
                        <th>Bookings</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($events as $event)
                        <tr>
                            <td>{{ $event->title }}</td>
                            <td>{{ $event->date->format('d M Y') }}</td>
                            <td>{{ $event->bookings_count }}</td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-secondary dropdown-toggle" type="button"
                                            data-bs-toggle="dropdown" aria-expanded="false">
                                        Actions
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <button class="dropdown-item view-attendees-btn"
                                                    data-event-id="{{ $event->id }}"
                                                    data-event-title="{{ $event->title }}"
                                                    data-bs-toggle="offcanvas"
                                                    data-bs-target="#attendeesOffcanvas">
                                                Attendees
                                            </button>
                                        </li>
                                        <li><a class="dropdown-item" href="{{ route('events.show', $event) }}">View</a>
                                        </li>

                                        @can('update', $event)
                                            <li><a class="dropdown-item"
                                                   href="{{ route('events.edit', $event) }}">Edit</a></li>
                                        @endcan

                                        @can('delete', $event)
                                            <li>
                                                <form action="{{ route('events.destroy', $event) }}" method="POST"
                                                      onsubmit="return confirm('Are you sure you want to delete this event?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button
                                                        class="dropdown-item text-danger" {{ $event->bookings_count > 0 ? 'disabled' : '' }}>
                                                        Delete
                                                    </button>
                                                </form>
                                            </li>
                                        @endcan
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            <div class="offcanvas offcanvas-end" tabindex="-1" id="attendeesOffcanvas"
                 aria-labelledby="attendeesOffcanvasLabel">
                <div class="offcanvas-header">
                    <h5 id="attendeesOffcanvasLabel">Attendees</h5>
                    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"
                            aria-label="Close"></button>
                </div>
                <div class="offcanvas-body" id="attendeesOffcanvasBody">
                    <p class="text-muted">Loading attendees...</p>
                </div>
            </div>
        @else
            <p class="text-muted">You have not created any events yet.</p>
        @endif
    </div>
@endsection

@push('scripts')
    <script type="module">
        $(document).ready(function () {
            $('.view-attendees-btn').on('click', function () {
                let eventId = $(this).data('event-id');
                let eventTitle = $(this).data('event-title');

                $('#attendeesOffcanvasLabel').text('Attendees - ' + eventTitle);
                $('#attendeesOffcanvasBody').html('<p class="text-muted">Loading...</p>');

                $.ajax({
                    url: `/events/${eventId}/attendees`,
                    method: 'GET',
                    success: function (response) {
                        if (response.length > 0) {
                            let html = '<ul class="list-group">';
                            response.forEach(function (attendee) {
                                html += `
                                <li class="list-group-item">
                                    <strong>${attendee.name}</strong><br>
                                    <small class="text-muted">${attendee.email}</small>
                                </li>`;
                            });
                            html += '</ul>';
                            $('#attendeesOffcanvasBody').html(html);
                        } else {
                            $('#attendeesOffcanvasBody').html('<p class="text-muted">No attendees yet.</p>');
                        }
                    },
                    error: function () {
                        $('#attendeesOffcanvasBody').html('<p class="text-danger">Failed to load attendees.</p>');
                    }
                });
            });
        });
    </script>
@endpush
