@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Request a Ride</h2>

    <form action="{{ route('customer.rides.store') }}" method="POST">
        @csrf

        {{-- Pickup Location --}}
        <div class="form-group">
            <label for="pickup_location">Pickup Location:</label>
            <input type="text" id="pickup_location" name="pickup_location" class="form-control" placeholder="Enter pickup location" required>
            <button type="button" id="current-location-btn" class="btn btn-secondary mt-2">Use Current Location</button>
        </div>

        {{-- Drop-off Location --}}
        <div class="form-group">
            <label for="destination">Drop-off Location:</label>
            <input type="text" id="destination" name="destination" class="form-control" placeholder="Enter drop-off location" required>
        </div>

        {{-- Hidden Fields for Coordinates --}}
        <input type="hidden" id="pickup_coordinates" name="pickup_coordinates">
        <input type="hidden" id="destination_coordinates" name="destination_coordinates">

        {{-- Map Display --}}
        <div id="map" style="width: 100%; height: 400px;"></div>

        {{-- Fare Display --}}
        <div class="form-group mt-3">
            <label for="fare">Estimated Fare:</label>
            <input type="text" id="fare" class="form-control" readonly>
        </div>

        {{-- Booking Button --}}
        <button type="submit" class="btn btn-primary mt-3">Book Now</button>
    </form>
</div>

{{-- Include Google Maps API --}}
<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCfDf_ytgEm4YDsLdzVqUcZJ4mV-cqyKTw&libraries=places&callback=initMap"></script>

<script>
    let map, pickupMarker, dropoffMarker;
    let pickupLocationInput = document.getElementById('pickup_location');
    let dropoffLocationInput = document.getElementById('destination');
    let pickupCoordinates = document.getElementById('pickup_coordinates');
    let dropoffCoordinates = document.getElementById('destination_coordinates');
    let fareField = document.getElementById('fare');

    function initMap() {
        map = new google.maps.Map(document.getElementById('map'), {
            center: { lat: -1.286389, lng: 36.817223 }, // Default center (Nairobi)
            zoom: 14,
        });

        let pickupAutocomplete = new google.maps.places.Autocomplete(pickupLocationInput);
        let dropoffAutocomplete = new google.maps.places.Autocomplete(dropoffLocationInput);

        pickupAutocomplete.addListener('place_changed', function() {
            let place = pickupAutocomplete.getPlace();
            if (!place.geometry) return;

            if (pickupMarker) pickupMarker.setMap(null);
            pickupMarker = new google.maps.Marker({
                position: place.geometry.location,
                map: map,
                title: 'Pickup Location',
            });

            pickupCoordinates.value = place.geometry.location.lat() + ',' + place.geometry.location.lng();
            map.setCenter(place.geometry.location);
        });

        dropoffAutocomplete.addListener('place_changed', function() {
            let place = dropoffAutocomplete.getPlace();
            if (!place.geometry) return;

            if (dropoffMarker) dropoffMarker.setMap(null);
            dropoffMarker = new google.maps.Marker({
                position: place.geometry.location,
                map: map,
                title: 'Drop-off Location',
            });

            dropoffCoordinates.value = place.geometry.location.lat() + ',' + place.geometry.location.lng();
            calculateFare();
        });

        document.getElementById('current-location-btn').addEventListener('click', function() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    let lat = position.coords.latitude;
                    let lng = position.coords.longitude;
                    let location = new google.maps.LatLng(lat, lng);

                    if (pickupMarker) pickupMarker.setMap(null);
                    pickupMarker = new google.maps.Marker({
                        position: location,
                        map: map,
                        title: 'Current Location',
                    });

                    map.setCenter(location);
                    pickupCoordinates.value = lat + ',' + lng;

                    let geocoder = new google.maps.Geocoder();
                    geocoder.geocode({ 'location': location }, function(results, status) {
                        if (status === 'OK' && results[0]) {
                            pickupLocationInput.value = results[0].formatted_address;
                        }
                    });
                });
            } else {
                alert("Geolocation is not supported by this browser.");
            }
        });
    }

    function calculateFare() {
        if (pickupCoordinates.value && dropoffCoordinates.value) {
            let [pickupLat, pickupLng] = pickupCoordinates.value.split(',').map(Number);
            let [dropoffLat, dropoffLng] = dropoffCoordinates.value.split(',').map(Number);

            let distance = haversineDistance(pickupLat, pickupLng, dropoffLat, dropoffLng);
            let fare = Math.ceil(distance / 10) * 50; // 50 per 10km
            fareField.value = `KSh ${fare}`;
        }
    }

    function haversineDistance(lat1, lon1, lat2, lon2) {
        const R = 6371; // Earth radius in km
        let dLat = (lat2 - lat1) * Math.PI / 180;
        let dLon = (lon2 - lon1) * Math.PI / 180;
        let a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                Math.sin(dLon / 2) * Math.sin(dLon / 2);
        let c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
        return R * c;
    }
</script>
@endsection
