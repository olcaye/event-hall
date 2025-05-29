@extends('layouts.app')
@php
    $intervals = [];
    $startTime = \Carbon\Carbon::createFromTime();
    $endTime = \Carbon\Carbon::createFromTime(23, 30);

    while ($startTime <= $endTime) {
        $intervals[] = $startTime->format('H:i');
        $startTime->addMinutes(30);
    }
@endphp
@section('content')
    <div class="container">
        <h2>Create Event</h2>

        <form id="createEventForm" action="{{ route('events.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="mb-3">
                <label for="title" class="form-label">Event Title</label>
                <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                       value="{{ old('title') }}" required>

                @error('title')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
            </div>


            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="4"
                          required>{{ old('description') }}</textarea>
                @error('description')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="location" class="form-label">Location</label>
                <input type="text" id="locationInput" name="location"
                       class="form-control @error('location') is-invalid @enderror" value="{{ old('location') }}"
                       required>
                @error('location')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
            </div>


            <div id="map" style="height: 300px;" class="mb-3"></div>
            <input type="hidden" name="latitude" id="latitude" value="{{ old('latitude') }}">
            <input type="hidden" name="longitude" id="longitude" value="{{ old('longitude') }}">


            @error('latitude')
            <div class="text-danger mb-1 small">
                {{ $message }} (Select a valid location from the map.)
            </div>
            @enderror
            @error('longitude')
            <div class="text-danger mb-3 small">
                {{ $message }} (Select a valid location from the map.)
            </div>
            @enderror


            <div class="mb-3">
                <label for="date" class="form-label">Date</label>
                <input type="date" name="date" class="form-control @error('date') is-invalid @enderror"
                       value="{{ old('date') }}" required>
                @error('date')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
            </div>


            <div class="mb-3">
                <label for="start_time" class="form-label">Start Time</label>
                <select name="start_time" id="start_time" class="form-select @error('start_time') is-invalid @enderror">
                    <option value="">Select Start Time</option>
                    @foreach($intervals as $time)
                        <option value="{{ $time }}" {{ old('start_time') == $time ? 'selected' : '' }}>
                            {{ $time }}
                        </option>
                    @endforeach
                </select>
                @error('start_time')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="end_time" class="form-label">End Time</label>
                <select name="end_time" id="end_time" class="form-select @error('end_time') is-invalid @enderror">
                    <option value="">Select End Time</option>
                    @foreach($intervals as $time)
                        <option value="{{ $time }}" {{ old('end_time') == $time ? 'selected' : '' }}>
                            {{ $time }}
                        </option>
                    @endforeach
                </select>
                @error('end_time')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="booking_limit" class="form-label">Booking Limit (optional)</label>
                <input type="number" name="booking_limit"
                       class="form-control @error('booking_limit') is-invalid @enderror" min="1"
                       value="{{ old('booking_limit') }}">
                @error('booking_limit')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="cover_image" class="form-label">Cover Image</label>
                <input type="file" name="cover_image" class="form-control @error('cover_image') is-invalid @enderror">
                @error('cover_image')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
            </div>

            <hr>

            <div class="mb-3">
                <label for="gallery" class="form-label">Gallery Images</label>
                <input type="file" name="gallery[]" id="gallery"
                       class="filepond @if($errors->has('gallery') || $errors->has('gallery.*')) is-invalid @endif"
                       multiple>
                @error('gallery')
                <div class="invalid-feedback d-block">
                    {{ $message }}
                </div>
                @enderror
                @error('gallery.*')
                <div class="invalid-feedback d-block">
                    {{ $message }}
                </div>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary">Create</button>
        </form>
    </div>
@endsection

@push('scripts')

    <script type="module">

        const inputElement = document.querySelector('#gallery');

        FilePond.create(inputElement, {
            allowMultiple: true,
            acceptedFileTypes: ['image/jpeg', 'image/png', 'image/gif', 'image/webp'],
            maxFileSize: '2MB',
            instantUpload: false,
            storeAsFile: true,
            labelIdle: 'Drag & Drop your images or <span class="filepond--label-action">Browse</span>',
            labelFileWaitingForSize: 'Waiting for size',
            labelFileSizeNotAvailable: 'Size not available',
            labelFileLoading: 'Loading',
            labelFileLoadError: 'Error during load',
            labelFileProcessing: 'Uploading',
            labelFileProcessingComplete: 'Upload complete',
            labelFileProcessingAborted: 'Upload cancelled',
            labelFileProcessingError: 'Error during upload',
            labelFileProcessingRevertError: 'Error during revert',
            labelFileRemove: 'Remove',
            labelTapToCancel: 'tap to cancel',
            labelTapToRetry: 'tap to retry',
            labelTapToUpload: 'tap to upload',
            fileValidateTypeLabelExpectedTypes: 'Expects {allButLastType} or {lastType}',
        });

    </script>

    <script>

        let map, marker;

        function initializeMap() {
            const defaultLatLng = {lat: 41.0082, lng: 28.9784};
            map = new google.maps.Map(document.getElementById("map"), {
                zoom: 10,
                center: defaultLatLng,
            });

            marker = new google.maps.Marker({
                position: defaultLatLng,
                map,
                draggable: true,
            });

            google.maps.event.addListener(marker, 'dragend', function (evt) {
                document.getElementById("latitude").value = evt.latLng.lat();
                document.getElementById("longitude").value = evt.latLng.lng();
            });
        }

        function initAutocomplete() {
            const input = document.getElementById('locationInput');
            const autocomplete = new google.maps.places.Autocomplete(input);
            autocomplete.addListener('place_changed', () => {
                const place = autocomplete.getPlace();
                if (place.geometry) {
                    const location = place.geometry.location;
                    const lat = location.lat();
                    const lng = location.lng();

                    map.setCenter({lat, lng});
                    marker.setPosition({lat, lng});

                    document.getElementById("latitude").value = lat;
                    document.getElementById("longitude").value = lng;
                }
            });
        }

        window.initMap = function () {
            initializeMap();
            initAutocomplete();
        };
    </script>

    <script
        src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.key') }}&libraries=places&callback=initMap"
        async defer></script>
@endpush
