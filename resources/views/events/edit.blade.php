@extends('layouts.app')

@php
    $intervals = [];
    $startTime = \Carbon\Carbon::createFromTime(0, 0);
    $endTime = \Carbon\Carbon::createFromTime(23, 30);

    while ($startTime <= $endTime) {
        $intervals[] = $startTime->format('H:i');
        $startTime->addMinutes(30);
    }
@endphp

@section('content')
    <div class="container">
        <h2>Edit Event</h2>

        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('events.update', $event) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')


            <div class="mb-3">
                <label class="form-label">Title</label>
                <input type="text" name="title" class="form-control" value="{{ old('title', $event->title) }}" required>
            </div>


            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="4"
                          required>{{ old('description', $event->description) }}</textarea>
            </div>


            <div class="mb-3">
                <label class="form-label">Location</label>
                <input type="text" id="locationInput" name="location" class="form-control"
                       value="{{ old('location', $event->location) }}" required>
            </div>


            <div id="map" style="height: 300px;" class="mb-3"></div>
            <input type="hidden" name="latitude" id="latitude" value="{{ old('latitude', $event->latitude) }}">
            <input type="hidden" name="longitude" id="longitude" value="{{ old('longitude', $event->longitude) }}">


            <div class="mb-3">
                <label class="form-label">Date</label>
                <input type="date" name="date" class="form-control"
                       value="{{ old('date', $event->date->format('Y-m-d')) }}" required>
            </div>


            <div class="mb-3">
                <label class="form-label">Start Time</label>
                <input type="time" name="start_time" class="form-control"
                       value="{{ old('start_time', optional($event->start_time)->format('H:i')) }}" step="1800">
            </div>

            <div class="mb-3">
                <label class="form-label">End Time</label>
                <input type="time" name="end_time" class="form-control"
                       value="{{ old('end_time', optional($event->end_time)->format('H:i')) }}" step="1800">
            </div>


            <div class="mb-3">
                <label class="form-label">Booking Limit (optional)</label>
                <input type="number" name="booking_limit" class="form-control" min="1"
                       value="{{ old('booking_limit', $event->booking_limit) }}">
            </div>


            <div class="mb-3">
                <label for="cover_image" class="form-label">Cover Image</label>
                <input type="file" name="cover_image" class="filepond" accept="image/*">
                @if($event->cover_image)
                    <div class="mt-2">
                        <img src="{{ Storage::url($event->cover_image) }}" alt="Cover Image" style="max-width: 200px;">
                    </div>
                @endif
            </div>

            <hr>

            <div class="mb-3">
                <label class="form-label">Event Images (Gallery)</label>
                <div id="dropzone-area" class="dropzone border border-dashed rounded p-3">
                    <div class="dz-message">Görselleri buraya sürükleyip bırakın veya tıklayın</div>
                </div>
            </div>


            <button type="submit" class="btn btn-primary">Update Event</button>
        </form>


    </div>
@endsection


@push('scripts')

    <script type="module">
        Dropzone.autoDiscover = false;

        $(function () {
            new Dropzone("#dropzone-area", {
                url: "{{ route('events.images.upload', $event) }}",
                paramName: "file",
                maxFilesize: 2, // MB
                acceptedFiles: ".jpeg,.jpg,.png,.gif,.webp",
                addRemoveLinks: true,
                timeout: 50000,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dictDefaultMessage: "Drag and drop images here or click to upload",
                dictRemoveFile: "Delete image",
                dictFileTooBig: "File is too big (@@filesizeMB). Maximum size: @@maxFilesizeMB.",
                init: function () {
                    var dropzone = this;

                    @if ($event->gallery && count($event->gallery) > 0)
                    @foreach($event->gallery as $filepath)
                    var mockFile = {
                        name: "{{ basename($filepath) }}",
                        size: 12345,
                        accepted: true,
                        serverFileName: "{{ $filepath }}"
                    };
                    dropzone.emit("addedfile", mockFile);
                    dropzone.emit("thumbnail", mockFile, "{{ Storage::url($filepath) }}");
                    dropzone.emit("complete", mockFile);
                    dropzone.files.push(mockFile);
                    @endforeach
                        @endif

                        this.on("success", function (file, response) {
                        console.log("Upload Success:", response);

                        file.serverFileName = response.filepath;
                    });


                    this.on("removedfile", function (file) {
                        console.log('Removed file:', file.serverFileName);
                        if (file.serverFileName) {
                            $.ajax({
                                url: "{{ route('events.images.delete', $event) }}",
                                type: 'DELETE',
                                data: {
                                    _token: $('meta[name="csrf-token"]').attr('content'),
                                    filepath: file.serverFileName
                                },
                                success: function (response) {
                                    console.log('Image deleted:', response);
                                },
                                error: function (xhr, status, error) {
                                    console.error('Error deleting image:', xhr.responseText);
                                }
                            });
                        }
                    });
                }
            });
        });
    </script>
    <script>
        let map, marker;

        function initMap() {
            const lat = parseFloat(document.getElementById('latitude').value) || 41.0082;
            const lng = parseFloat(document.getElementById('longitude').value) || 28.9784;
            const defaultLatLng = {lat, lng};

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

        window.handleMapReady = function () {
            initMap();
            initAutocomplete();
        };
    </script>
    <script
        src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.key') }}&libraries=places&callback=handleMapReady"
        async defer></script>
@endpush
