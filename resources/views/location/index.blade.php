@extends('layouts.app')

@section('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
@endsection

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    <h2 class="text-center">Ma Localisation</h2>
                </div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <div id="location-error" class="alert alert-danger d-none" role="alert">
                        Une erreur s'est produite lors de la récupération de votre position.
                    </div>

                    <div class="text-center mb-3">
                        <button id="get-location-btn" class="btn btn-primary">
                            <i class="fas fa-map-marker-alt"></i> Actualiser ma position
                        </button>
                    </div>

                    <div id="map" style="height: 500px; width: 100%; border-radius: 5px;"></div>

                    <div id="coordinates-container" class="mt-4 p-3 bg-light rounded d-none">
                        <h4>Coordonnées</h4>
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Latitude:</strong> <span id="latitude"></span></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Longitude:</strong> <span id="longitude"></span></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="{{ asset('js/location.js') }}"></script>
@endsection

