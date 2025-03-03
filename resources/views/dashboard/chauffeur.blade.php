@extends('layouts.app')

@section('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    .tab-container {
        background: #f8fafc;
        padding: 20px;
        border-radius: 12px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
    }

    .nav-tabs {
        border-bottom: 1px solid #e5e7eb;
        margin-bottom: 20px;
        display: flex;
        gap: 1rem;
        padding: 0 1rem;
    }

    .nav-tabs .nav-link {
        padding: 1rem;
        color: #6b7280;
        border: none;
        position: relative;
        font-weight: 500;
        cursor: pointer;
        transition: color 0.2s ease;
    }

    .nav-tabs .nav-link:hover {
        color: #3b82f6;
    }

    .nav-tabs .nav-link.active {
        color: #3b82f6;
        background: none;
    }

    .nav-tabs .nav-link.active::after {
        content: '';
        position: absolute;
        bottom: -1px;
        left: 0;
        right: 0;
        height: 2px;
        background: #3b82f6;
    }

    .tab-content {
        padding: 1rem;
    }

    #map {
        height: 400px;
        width: 100%;
        border-radius: 0.5rem;
        margin-bottom: 1rem;
        border: 1px solid #e5e7eb;
    }

    .announcement-map {
        height: 200px;
        width: 100%;
        border-radius: 0.5rem;
        margin: 1rem 0;
    }

    .card {
        background: white;
        border-radius: 0.5rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        border: 1px solid #e5e7eb;
        margin-bottom: 1rem;
    }

    .card-header {
        padding: 1rem;
        border-bottom: 1px solid #e5e7eb;
        background: #f9fafb;
        border-top-left-radius: 0.5rem;
        border-top-right-radius: 0.5rem;
    }

    .card-body {
        padding: 1rem;
    }

    .card-footer {
        padding: 1rem;
        border-top: 1px solid #e5e7eb;
        background: #f9fafb;
        border-bottom-left-radius: 0.5rem;
        border-bottom-right-radius: 0.5rem;
    }

    .btn-primary {
        background: #3b82f6;
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 0.375rem;
        border: none;
        font-weight: 500;
        transition: background 0.2s ease;
    }

    .btn-primary:hover {
        background: #2563eb;
    }

    .btn-primary:disabled {
        background: #9ca3af;
        cursor: not-allowed;
    }

    .btn-warning {
        background: #f59e0b;
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 0.375rem;
        border: none;
        font-weight: 500;
        transition: background 0.2s ease;
    }

    .btn-warning:hover {
        background: #d97706;
    }

    .btn-success {
        background: #10b981;
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 0.375rem;
        border: none;
        font-weight: 500;
        transition: background 0.2s ease;
    }

    .btn-success:hover {
        background: #059669;
    }

    .btn-danger {
        background: #ef4444;
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 0.375rem;
        border: none;
        font-weight: 500;
        transition: background 0.2s ease;
    }

    .btn-danger:hover {
        background: #dc2626;
    }

    .btn-outline-primary {
        color: #3b82f6;
        border: 1px solid #3b82f6;
        background: transparent;
        padding: 0.5rem 1rem;
        border-radius: 0.375rem;
        font-weight: 500;
        transition: all 0.2s ease;
    }

    .btn-outline-primary:hover {
        background: #3b82f6;
        color: white;
    }

    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
    }

    .location-info {
        margin-top: 1rem;
        padding: 1rem;
        background: #f9fafb;
        border-radius: 0.375rem;
        border: 1px solid #e5e7eb;
    }

    .form-label {
        display: block;
        font-size: 0.875rem;
        font-weight: 500;
        color: #374151;
        margin-bottom: 0.5rem;
    }

    .form-control {
        width: 100%;
        padding: 0.5rem;
        border: 1px solid #e5e7eb;
        border-radius: 0.375rem;
        font-size: 0.875rem;
    }

    .form-control:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.1);
    }

    .status-badge {
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.875rem;
        font-weight: 500;
    }

    .status-active {
        background: #dcfce7;
        color: #166534;
    }

    .status-inactive {
        background: #fee2e2;
        color: #991b1b;
    }

    .status-pending {
        background: #fef3c7;
        color: #92400e;
    }

    .status-accepted {
        background: #d1fae5;
        color: #065f46;
    }

    .status-rejected {
        background: #fee2e2;
        color: #991b1b;
    }

    .status-cancelled {
        background: #f3f4f6;
        color: #374151;
    }

    .status-completed {
        background: #e0e7ff;
        color: #3730a3;
    }

    .notification-badge {
        position: absolute;
        top: 0;
        right: 0;
        transform: translate(50%, -50%);
        background: #ef4444;
        color: white;
        padding: 0.25rem 0.5rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 500;
    }

    .notification-item {
        padding: 1rem;
        border-bottom: 1px solid #e5e7eb;
    }

    .notification-item:last-child {
        border-bottom: none;
    }

    .notification-header {
        display: flex;
        justify-content: space-between;
        align-items: start;
        margin-bottom: 0.5rem;
    }

    .notification-title {
        font-weight: 500;
        color: #1f2937;
    }

    .notification-time {
        font-size: 0.875rem;
        color: #6b7280;
    }

    .notification-content {
        color: #4b5563;
        margin-bottom: 0.5rem;
    }

    .notification-details {
        background: #f3f4f6;
        padding: 0.75rem;
        border-radius: 0.375rem;
        font-size: 0.875rem;
    }

    .empty-state {
        text-align: center;
        padding: 3rem 1rem;
    }

    .empty-state i {
        font-size: 3rem;
        color: #9ca3af;
        margin-bottom: 1rem;
    }

    .empty-state h5 {
        color: #374151;
        margin-bottom: 0.5rem;
    }

    .empty-state p {
        color: #6b7280;
    }

    .alert {
        padding: 1rem;
        border-radius: 0.375rem;
        margin-bottom: 1rem;
    }

    .alert-danger {
        background: #fee2e2;
        color: #991b1b;
        border: 1px solid #fecaca;
    }

    .alert-success {
        background: #dcfce7;
        color: #166534;
        border: 1px solid #bbf7d0;
    }

    .btn-group {
        display: flex;
        gap: 0.5rem;
    }

    .text-muted {
        color: #6b7280;
    }

    .mb-3 {
        margin-bottom: 1rem;
    }

    .d-flex {
        display: flex;
    }

    .justify-content-between {
        justify-content: space-between;
    }

    .align-items-center {
        align-items: center;
    }

    .w-100 {
        width: 100%;
    }

    .gap-2 {
        gap: 0.5rem;
    }

    .me-2 {
        margin-right: 0.5rem;
    }

    .text-center {
        text-align: center;
    }

    .btn-link {
        background: none;
        border: none;
        color: #3b82f6;
        padding: 0;
        font-weight: 500;
        cursor: pointer;
        text-decoration: underline;
    }

    .btn-link:hover {
        color: #2563eb;
    }

    .loading-spinner {
        display: inline-block;
        width: 1rem;
        height: 1rem;
        border: 2px solid rgba(255, 255, 255, 0.3);
        border-radius: 50%;
        border-top-color: #fff;
        animation: spin 1s ease-in-out infinite;
    }

    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    .route-info {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .route-point {
        display: flex;
        align-items: flex-start;
        gap: 0.5rem;
    }

    .route-point i {
        margin-top: 0.25rem;
    }

    .route-point-content {
        flex: 1;
    }

    .route-point-label {
        font-size: 0.75rem;
        color: #6b7280;
        margin-bottom: 0.25rem;
    }

    .route-point-address {
        font-size: 0.875rem;
        color: #1f2937;
    }
</style>
@endsection

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-12">
            <div class="tab-container">
                <ul class="nav nav-tabs" id="driverTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="announcements-tab" data-bs-toggle="tab"
                            data-bs-target="#announcements" type="button" role="tab">
                            <i class="fas fa-bullhorn me-2"></i>Mes Annonces
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="position-tab" data-bs-toggle="tab"
                            data-bs-target="#position" type="button" role="tab">
                            <i class="fas fa-map-marker-alt me-2"></i>Ma Position
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="requests-tab" data-bs-toggle="tab"
                            data-bs-target="#requests" type="button" role="tab">
                            <i class="fas fa-hand-paper me-2"></i>Demandes
                            <span class="notification-badge" id="requestsCount" style="display: none;">0</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="notifications-tab" data-bs-toggle="tab"
                            data-bs-target="#notifications" type="button" role="tab">
                            <i class="fas fa-bell me-2"></i>Notifications
                            <span class="notification-badge" id="notificationsCount" style="display: none;">0</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="reservations-tab" data-bs-toggle="tab"
                            data-bs-target="#reservations" type="button" role="tab">
                            <i class="fas fa-calendar me-2"></i>Réservations
                        </button>
                    </li>
                </ul>

                <div class="tab-content" id="driverTabsContent">
                    <!-- Onglet Mes Annonces -->
                    <div class="tab-pane fade show active" id="announcements" role="tabpanel">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0">Mes annonces actives</h5>
                                <button id="refresh-announcements" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-sync-alt me-2"></i>Actualiser
                                </button>
                            </div>
                            <div class="card-body">
                                <div id="announcements-list">
                                    <div class="text-center py-4">
                                        <div class="spinner-border text-primary" role="status">
                                            <span class="visually-hidden">Chargement...</span>
                                        </div>
                                        <p class="mt-2">Chargement des annonces...</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Onglet Ma Position -->
                    <div class="tab-pane fade" id="position" role="tabpanel">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Ma position actuelle</h5>
                            </div>
                            <div class="card-body">
                                <div class="text-center mb-3">
                                    <button id="locate-btn" class="btn btn-primary">
                                        <i class="fas fa-location-arrow me-2"></i>Actualiser ma position
                                    </button>
                                </div>
                                <div id="map"></div>
                                <div class="location-info">
                                    <div class="mb-3">
                                        <label class="form-label">Adresse actuelle</label>
                                        <input type="text" id="current-address" class="form-control" readonly>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Note (optionnel)</label>
                                        <textarea id="location-note" class="form-control" rows="3"
                                            placeholder="Ajoutez des informations supplémentaires..."></textarea>
                                    </div>
                                    <button id="publish-btn" class="btn btn-primary w-100" disabled>
                                        <i class="fas fa-broadcast-tower me-2"></i>Publier ma position
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Onglet Demandes -->
                    <div class="tab-pane fade" id="requests" role="tabpanel">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0">Demandes en attente</h5>
                                <button id="refresh-requests" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-sync-alt me-2"></i>Actualiser
                                </button>
                            </div>
                            <div class="card-body">
                                <div id="requests-list">
                                    <div class="text-center py-4">
                                        <div class="spinner-border text-primary" role="status">
                                            <span class="visually-hidden">Chargement...</span>
                                        </div>
                                        <p class="mt-2">Chargement des demandes...</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Onglet Notifications -->
                    <div class="tab-pane fade" id="notifications" role="tabpanel">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0">Notifications</h5>
                                <div>
                                    <button id="refresh-notifications" class="btn btn-sm btn-outline-primary me-2">
                                        <i class="fas fa-sync-alt me-2"></i>Actualiser
                                    </button>
                                    <button id="mark-all-read" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-check-double me-2"></i>Tout marquer comme lu
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div id="notifications-list">
                                    <div class="text-center py-4">
                                        <div class="spinner-border text-primary" role="status">
                                            <span class="visually-hidden">Chargement...</span>
                                        </div>
                                        <p class="mt-2">Chargement des notifications...</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Onglet Réservations -->
                    <div class="tab-pane fade" id="reservations" role="tabpanel">
                        <div class="card">
                            <div class="card-header">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h5 class="card-title mb-0">Réservations</h5>
                                    <div>
                                        <button id="refresh-reservations" class="btn btn-sm btn-outline-primary me-2">
                                            <i class="fas fa-sync-alt me-2"></i>Actualiser
                                        </button>
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-outline-primary btn-sm active" data-filter="all">
                                                Toutes
                                            </button>
                                            <button type="button" class="btn btn-outline-primary btn-sm" data-filter="pending">
                                                En attente
                                            </button>
                                            <button type="button" class="btn btn-outline-primary btn-sm" data-filter="accepted">
                                                Acceptées
                                            </button>
                                            <button type="button" class="btn btn-outline-primary btn-sm" data-filter="others">
                                                Autres
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div id="reservations-list">
                                    <div class="text-center py-4">
                                        <div class="spinner-border text-primary" role="status">
                                            <span class="visually-hidden">Chargement...</span>
                                        </div>
                                        <p class="mt-2">Chargement des réservations...</p>
                                    </div>
                                </div>
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
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Variables globales
    let map = null;
    let marker = null;
    let currentPosition = null;
    let announcementMaps = new Map();
    let allRides = [];
    let currentFilter = 'all';
    let userId = {{ auth()->id() }};

    // Initialiser les onglets Bootstrap
    const triggerTabList = document.querySelectorAll('#driverTabs button');
    triggerTabList.forEach(triggerEl => {
        const tabTrigger = new bootstrap.Tab(triggerEl);

        triggerEl.addEventListener('click', event => {
            event.preventDefault();
            tabTrigger.show();
        });

        // Initialiser la carte quand l'onglet position est affiché
        triggerEl.addEventListener('shown.bs.tab', event => {
            if (event.target.id === 'position-tab') {
                setTimeout(() => {
                    initMap();
                    map.invalidateSize();
                    if (currentPosition) {
                        map.setView([currentPosition.coords.latitude, currentPosition.coords.longitude], 15);
                    }
                }, 100);
            }
        });
    });

    // Initialiser la carte principale
    function initMap() {
        if (map) return;

        map = L.map('map').setView([31.7917, -7.0926], 13);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);
    }

    // Obtenir la position actuelle
    document.getElementById('locate-btn').addEventListener('click', function() {
        if (!navigator.geolocation) {
            showAlert('error', "La géolocalisation n'est pas supportée par votre navigateur.");
            return;
        }

        this.disabled = true;
        this.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Localisation en cours...';

        navigator.geolocation.getCurrentPosition(
            function(position) {
                currentPosition = position;
                const { latitude, longitude } = position.coords;

                if (marker) {
                    marker.setLatLng([latitude, longitude]);
                } else {
                    marker = L.marker([latitude, longitude]).addTo(map);
                }

                map.setView([latitude, longitude], 15);

                // Obtenir l'adresse
                fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${latitude}&lon=${longitude}`)
                    .then(response => response.json())
                    .then(data => {
                        document.getElementById('current-address').value = data.display_name;
                        document.getElementById('publish-btn').disabled = false;
                    })
                    .catch(error => {
                        console.error('Erreur lors de la récupération de l\'adresse:', error);
                        document.getElementById('current-address').value = `${latitude}, ${longitude}`;
                        document.getElementById('publish-btn').disabled = false;
                    });

                document.getElementById('locate-btn').disabled = false;
                document.getElementById('locate-btn').innerHTML = '<i class="fas fa-location-arrow me-2"></i>Actualiser ma position';
            },
            function(error) {
                let errorMessage = "Erreur lors de la récupération de votre position.";
                switch(error.code) {
                    case error.PERMISSION_DENIED:
                        errorMessage = "Vous devez autoriser la géolocalisation.";
                        break;
                    case error.POSITION_UNAVAILABLE:
                        errorMessage = "Position non disponible.";
                        break;
                    case error.TIMEOUT:
                        errorMessage = "Délai d'attente dépassé.";
                        break;
                }
                showAlert('error', errorMessage);
                document.getElementById('locate-btn').disabled = false;
                document.getElementById('locate-btn').innerHTML = '<i class="fas fa-location-arrow me-2"></i>Actualiser ma position';
            },
            {
                enableHighAccuracy: true,
                timeout: 10000,
                maximumAge: 0
            }
        );
    });

    // Publier la position
    document.getElementById('publish-btn').addEventListener('click', async function() {
        if (!currentPosition) {
            showAlert('error', "Veuillez d'abord obtenir votre position.");
            return;
        }

        const button = this;
        button.disabled = true;
        button.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Publication en cours...';

        try {
            const response = await fetch('/driver/announce', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    latitude: currentPosition.coords.latitude,
                    longitude: currentPosition.coords.longitude,
                    location_name: document.getElementById('current-address').value,
                    note: document.getElementById('location-note').value
                })
            });

            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.message || 'Erreur lors de la publication');
            }

            // Réinitialiser le formulaire
            document.getElementById('location-note').value = '';
            button.disabled = true;
            button.innerHTML = '<i class="fas fa-broadcast-tower me-2"></i>Publier ma position';

            // Afficher le message de succès
            showAlert('success', 'Position publiée avec succès!');

            // Recharger les annonces
            loadAnnouncements();

            // Basculer vers l'onglet des annonces après 1 seconde
            setTimeout(() => {
                const announcementsTab = document.querySelector('button[data-bs-target="#announcements"]');
                const tab = new bootstrap.Tab(announcementsTab);
                tab.show();
            }, 1000);

        } catch (error) {
            showAlert('error', error.message);
            button.disabled = false;
            button.innerHTML = '<i class="fas fa-broadcast-tower me-2"></i>Publier ma position';
        }
    });

    // Fonction pour afficher des alertes
    function showAlert(type, message) {
        const alertClass = type === 'error' ? 'alert-danger' : 'alert-success';
        const icon = type === 'error' ? 'exclamation-circle' : 'check-circle';

        const alert = document.createElement('div');
        alert.className = `alert ${alertClass} alert-dismissible fade show`;
        alert.innerHTML = `
            <i class="fas fa-${icon} me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;

        // Trouver l'onglet actif
        const activeTab = document.querySelector('.tab-pane.active');
        const cardBody = activeTab.querySelector('.card-body');

        // Insérer l'alerte au début du card-body
        cardBody.insertBefore(alert, cardBody.firstChild);

        // Supprimer automatiquement après 5 secondes
        setTimeout(() => {
            alert.classList.remove('show');
            setTimeout(() => alert.remove(), 150);
        }, 5000);
    }

    // Charger les annonces
    async function loadAnnouncements() {
        const container = document.getElementById('announcements-list');
        container.innerHTML = `
            <div class="text-center py-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Chargement...</span>
                </div>
                <p class="mt-2">Chargement des annonces...</p>
            </div>
        `;

        try {
            const response = await fetch('/driver/announcements');

            if (!response.ok) {
                throw new Error(`Erreur HTTP: ${response.status}`);
            }

            const announcements = await response.json();

            // Nettoyer les anciennes cartes
            announcementMaps.forEach(map => map.remove());
            announcementMaps.clear();

            if (!announcements || announcements.length === 0) {
                container.innerHTML = `
                    <div class="empty-state">
                        <i class="fas fa-map-marker-alt"></i>
                        <h5>Aucune annonce active</h5>
                        <p>Utilisez l'onglet "Ma Position" pour publier votre position.</p>
                    </div>
                `;
                return;
            }

            container.innerHTML = announcements.map(announcement => `
                <div class="card mb-3">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-0">${announcement.location_name}</h6>
                                <small class="text-muted">
                                    <i class="far fa-clock me-2"></i>
                                    Publié le ${new Date(announcement.created_at).toLocaleString()}
                                </small>
                            </div>
                            <span class="status-badge ${announcement.is_active ? 'status-active' : 'status-inactive'}">
                                ${announcement.is_active ? 'Active' : 'Inactive'}
                            </span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="announcement-map-${announcement.id}" class="announcement-map"></div>
                        ${announcement.note ? `
                            <div class="mt-3">
                                <i class="fas fa-info-circle me-2 text-primary"></i>
                                ${announcement.note}
                            </div>
                        ` : ''}
                    </div>
                    <div class="card-footer">
                        <button onclick="toggleAnnouncement(${announcement.id})"
                            class="btn btn-sm ${announcement.is_active ? 'btn-warning' : 'btn-success'}">
                            <i class="fas fa-${announcement.is_active ? 'pause' : 'play'} me-2"></i>
                            ${announcement.is_active ? 'Suspendre' : 'Réactiver'}
                        </button>
                        <button onclick="deleteAnnouncement(${announcement.id})"
                            class="btn btn-sm btn-danger ms-2">
                            <i class="fas fa-trash me-2"></i>Supprimer
                        </button>
                    </div>
                </div>
            `).join('');

            // Initialiser les cartes pour chaque annonce
            announcements.forEach(announcement => {
                const mapId = `announcement-map-${announcement.id}`;
                const announcementMap = L.map(mapId).setView([announcement.latitude, announcement.longitude], 15);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© OpenStreetMap contributors'
                }).addTo(announcementMap);
                L.marker([announcement.latitude, announcement.longitude]).addTo(announcementMap);

                // Désactiver le zoom pour éviter les conflits de scroll
                announcementMap.scrollWheelZoom.disable();

                announcementMaps.set(announcement.id, announcementMap);
            });

        } catch (error) {
            console.error('Erreur:', error);
            container.innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    Une erreur est survenue lors du chargement des annonces.
                    <button type="button" class="btn btn-link text-danger" onclick="loadAnnouncements()">
                        <i class="fas fa-sync-alt me-1"></i>Réessayer
                    </button>
                </div>
            `;
        }
    }

    // Basculer l'état d'une annonce
    window.toggleAnnouncement = async function(id) {
        try {
            const response = await fetch(`/driver/announcement/${id}/toggle`, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });

            if (!response.ok) {
                throw new Error('Erreur lors de la mise à jour');
            }

            const data = await response.json();
            showAlert('success', data.message || 'Annonce mise à jour avec succès');
            loadAnnouncements();
        } catch (error) {
            showAlert('error', error.message || 'Erreur lors de la mise à jour de l\'annonce');
        }
    };

    // Supprimer une annonce
    window.deleteAnnouncement = async function(id) {
        if (!confirm('Êtes-vous sûr de vouloir supprimer cette annonce ?')) {
            return;
        }

        try {
            const response = await fetch(`/driver/announcement/${id}`, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });

            if (!response.ok) {
                throw new Error('Erreur lors de la suppression');
            }

            const data = await response.json();
            showAlert('success', data.message || 'Annonce supprimée avec succès');
            loadAnnouncements();
        } catch (error) {
            showAlert('error', error.message || 'Erreur lors de la suppression de l\'annonce');
        }
    };

    // Charger les demandes en attente
    async function loadPendingRequests() {
        const container = document.getElementById('requests-list');
        container.innerHTML = `
            <div class="text-center py-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Chargement...</span>
                </div>
                <p class="mt-2">Chargement des demandes...</p>
            </div>
        `;

        try {
            const response = await fetch('/driver/pending-requests');

            if (!response.ok) {
                throw new Error(`Erreur HTTP: ${response.status}`);
            }

            const data = await response.json();

            if (!data.success) {
                throw new Error(data.error || 'Erreur lors du chargement des demandes');
            }

            const badge = document.getElementById('requestsCount');

            if (!data.requests || data.requests.length === 0) {
                badge.style.display = 'none';
                container.innerHTML = `
                    <div class="empty-state">
                        <i class="fas fa-inbox"></i>
                        <h5>Aucune demande en attente</h5>
                        <p>Vous n'avez pas de nouvelles demandes de course pour le moment.</p>
                    </div>
                `;
                return;
            }

            badge.textContent = data.requests.length;
            badge.style.display = 'block';

            container.innerHTML = data.requests.map(request => `
                <div class="card mb-3">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center gap-2">
                                <img src="${request.passenger.profile_image_url || '/images/default-avatar.png'}"
                                    alt="Photo de ${request.passenger.name}"
                                    class="rounded-circle"
                                    style="width: 40px; height: 40px; object-fit: cover;">
                                <div>
                                    <h6 class="mb-0">${request.passenger.name}</h6>
                                    <small class="text-muted">
                                        <i class="fas fa-phone me-1"></i>${request.passenger.phone}
                                    </small>
                                </div>
                            </div>
                            <span class="status-badge status-pending">En attente</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="text-muted mb-2">
                                <i class="far fa-calendar-alt me-2"></i>
                                Prévu pour le ${new Date(request.scheduled_at).toLocaleString()}
                            </div>
                            <div class="route-info">
                                <div class="route-point">
                                    <i class="fas fa-map-marker-alt text-success"></i>
                                    <div class="route-point-content">
                                        <div class="route-point-label">Départ</div>
                                        <div class="route-point-address">${request.pickup_address}</div>
                                    </div>
                                </div>
                                <div class="route-point">
                                    <i class="fas fa-flag-checkered text-danger"></i>
                                    <div class="route-point-content">
                                        <div class="route-point-label">Destination</div>
                                        <div class="route-point-address">${request.destination_address}</div>
                                    </div>
                                </div>
                            </div>
                            ${request.note ? `
                                <div class="mt-3 p-2 bg-light rounded">
                                    <i class="fas fa-info-circle me-2 text-primary"></i>
                                    ${request.note}
                                </div>
                            ` : ''}
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="d-flex justify-content-end gap-2">
                            <button onclick="respondToRequest(${request.id}, 'rejected')"
                                class="btn btn-danger btn-sm">
                                <i class="fas fa-times me-2"></i>Refuser
                            </button>
                            <button onclick="respondToRequest(${request.id}, 'accepted')"
                                class="btn btn-success btn-sm">
                                <i class="fas fa-check me-2"></i>Accepter
                            </button>
                        </div>
                    </div>
                </div>
            `).join('');
        } catch (error) {
            console.error('Erreur:', error);
            container.innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    ${error.message}
                    <button type="button" class="btn btn-link text-danger" onclick="loadPendingRequests()">
                        <i class="fas fa-sync-alt me-1"></i>Réessayer
                    </button>
                </div>
            `;
        }
    }

    // Répondre à une demande
    window.respondToRequest = async function(id, status) {
        if (!confirm(`Êtes-vous sûr de vouloir ${status === 'accepted' ? 'accepter' : 'refuser'} cette demande ?`)) {
            return;
        }

        try {
            const response = await fetch(`/driver/ride-request/${id}/respond`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ status })
            });

            if (!response.ok) {
                throw new Error(`Erreur HTTP: ${response.status}`);
            }

            const data = await response.json();

            if (!data.success) {
                throw new Error(data.error || 'Erreur lors de la réponse à la demande');
            }

            showAlert('success', data.message);
            loadPendingRequests();
            loadRides();
        } catch (error) {
            showAlert('error', error.message);
        }
    };

    // Charger les notifications
    async function loadNotifications() {
        const container = document.getElementById('notifications-list');
        container.innerHTML = `
            <div class="text-center py-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Chargement...</span>
                </div>
                <p class="mt-2">Chargement des notifications...</p>
            </div>
        `;

        try {
            const response = await fetch('/driver/notifications');

            if (!response.ok) {
                throw new Error(`Erreur HTTP: ${response.status}`);
            }

            const data = await response.json();

            if (!data.success) {
                throw new Error(data.error || 'Erreur lors du chargement des notifications');
            }

            const badge = document.getElementById('notificationsCount');

            if (!data.notifications || data.notifications.length === 0) {
                container.innerHTML = `
                    <div class="empty-state">
                        <i class="fas fa-bell-slash"></i>
                        <h5>Aucune notification</h5>
                        <p>Vous n'avez pas de notifications pour le moment.</p>
                    </div>
                `;
                badge.style.display = 'none';
                return;
            }

            const unreadCount = data.notifications.filter(n => !n.read_at).length;
            if (unreadCount > 0) {
                badge.textContent = unreadCount;
                badge.style.display = 'block';
            } else {
                badge.style.display = 'none';
            }

            container.innerHTML = data.notifications.map(notification => `
                <div class="notification-item ${!notification.read_at ? 'bg-light' : ''}">
                    <div class="notification-header">
                        <div class="notification-title">
                            <i class="fas fa-${getNotificationIcon(notification.type)} me-2"></i>
                            ${getNotificationTitle(notification.type)}
                        </div>
                        <div class="notification-time">
                            ${new Date(notification.created_at).toLocaleString()}
                        </div>
                    </div>
                    <div class="notification-content">
                        ${notification.message}
                    </div>
                    <div class="notification-details">
                        ${notification.passenger_name ? `
                            <p><i class="fas fa-user me-2"></i>Passager: ${notification.passenger_name}</p>
                        ` : ''}
                        ${notification.scheduled_at ? `
                            <p><i class="fas fa-calendar me-2"></i>Prévu pour: ${new Date(notification.scheduled_at).toLocaleString()}</p>
                        ` : ''}
                        ${notification.pickup_address ? `
                            <p><i class="fas fa-map-marker-alt me-2"></i>Départ: ${notification.pickup_address}</p>
                        ` : ''}
                        ${notification.destination_address ? `
                            <p><i class="fas fa-flag-checkered me-2"></i>Destination: ${notification.destination_address}</p>
                        ` : ''}
                    </div>
                </div>
            `).join('');
        } catch (error) {
            console.error('Erreur:', error);
            container.innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    ${error.message}
                    <button type="button" class="btn btn-link text-danger" onclick="loadNotifications()">
                        <i class="fas fa-sync-alt me-1"></i>Réessayer
                    </button>
                </div>
            `;
        }
    }

    // Marquer toutes les notifications comme lues
    window.markAllAsRead = async function() {
        try {
            const response = await fetch('/driver/notifications/mark-all-read', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });

            if (!response.ok) {
                throw new Error('Erreur lors du marquage des notifications');
            }

            const data = await response.json();
            showAlert('success', data.message || 'Toutes les notifications ont été marquées comme lues');
            loadNotifications();
        } catch (error) {
            showAlert('error', error.message);
        }
    };

    // Charger les réservations
    async function loadRides() {
        const container = document.getElementById('reservations-list');
        container.innerHTML = `
            <div class="text-center py-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Chargement...</span>
                </div>
                <p class="mt-2">Chargement des réservations...</p>
            </div>
        `;

        try {
            const response = await fetch('/driver/ride-requests');

            if (!response.ok) {
                throw new Error(`Erreur HTTP: ${response.status}`);
            }

            const data = await response.json();

            if (!data.success) {
                throw new Error(data.error || 'Erreur lors du chargement des réservations');
            }

            allRides = data.requests || [];
            filterRides(currentFilter);
        } catch (error) {
            console.error('Erreur:', error);
            container.innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    ${error.message}
                    <button type="button" class="btn btn-link text-danger" onclick="loadRides()">
                        <i class="fas fa-sync-alt me-1"></i>Réessayer
                    </button>
                </div>
            `;
        }
    }

    // Filtrer les réservations
    window.filterRides = function(filter) {
        currentFilter = filter;

        // Mettre à jour les boutons de filtre
        document.querySelectorAll('.btn-group button').forEach(btn => {
            btn.classList.remove('active');
        });
        document.querySelector(`.btn-group button[data-filter="${filter}"]`).classList.add('active');

        let filteredRides = allRides;

        switch (filter) {
            case 'pending':
                filteredRides = allRides.filter(ride => ride.status === 'pending');
                break;
            case 'accepted':
                filteredRides = allRides.filter(ride => ride.status === 'accepted');
                break;
            case 'others':
                filteredRides = allRides.filter(ride =>
                    !['pending', 'accepted'].includes(ride.status)
                );
                break;
        }

        displayRides(filteredRides);
    };

    // Afficher les réservations
    function displayRides(rides) {
        const container = document.getElementById('reservations-list');

        if (rides.length === 0) {
            container.innerHTML = `
                <div class="empty-state">
                    <i class="fas fa-calendar-times"></i>
                    <h5>Aucune réservation trouvée</h5>
                    <p>Il n'y a pas de réservations correspondant à vos critères.</p>
                </div>
            `;
            return;
        }

        container.innerHTML = rides.map(ride => `
            <div class="card mb-3">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-2">
                            <img src="${ride.passenger.profile_image_url || '/images/default-avatar.png'}"
                                alt="Photo de ${ride.passenger.name}"
                                class="rounded-circle"
                                style="width: 40px; height: 40px; object-fit: cover;">
                            <div>
                                <h6 class="mb-0">${ride.passenger.name}</h6>
                                <small class="text-muted">
                                    <i class="fas fa-phone me-1"></i>${ride.passenger.phone}
                                </small>
                            </div>
                        </div>
                        <span class="status-badge status-${ride.status}">
                            ${getStatusLabel(ride.status)}
                            ${ride.passenger_confirmation_at ? ' (Confirmé)' : ''}
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="text-muted mb-2">
                            <i class="far fa-calendar-alt me-2"></i>
                            Prévu pour le ${new Date(ride.scheduled_at).toLocaleString()}
                        </div>
                        <div class="route-info">
                            <div class="route-point">
                                <i class="fas fa-map-marker-alt text-success"></i>
                                <div class="route-point-content">
                                    <div class="route-point-label">Départ</div>
                                    <div class="route-point-address">${ride.pickup_address}</div>
                                </div>
                            </div>
                            <div class="route-point">
                                <i class="fas fa-flag-checkered text-danger"></i>
                                <div class="route-point-content">
                                    <div class="route-point-label">Destination</div>
                                    <div class="route-point-address">${ride.destination_address}</div>
                                </div>
                            </div>
                        </div>
                        ${ride.note ? `
                            <div class="mt-3 p-2 bg-light rounded">
                                <i class="fas fa-info-circle me-2 text-primary"></i>
                                ${ride.note}
                            </div>
                        ` : ''}
                    </div>
                </div>
                ${ride.status === 'pending' ? `
                    <div class="card-footer">
                        <div class="d-flex justify-content-end gap-2">
                            <button onclick="respondToRequest(${ride.id}, 'rejected')"
                                class="btn btn-danger btn-sm">
                                <i class="fas fa-times me-2"></i>Refuser
                            </button>
                            <button onclick="respondToRequest(${ride.id}, 'accepted')"
                                class="btn btn-success btn-sm">
                                <i class="fas fa-check me-2"></i>Accepter
                            </button>
                        </div>
                    </div>
                ` : ride.status === 'accepted' ? `
                    <div class="card-footer">
                        <div class="d-flex justify-content-end gap-2">
                            <button onclick="completeRide(${ride.id})"
                                class="btn btn-primary btn-sm">
                                <i class="fas fa-check-circle me-2"></i>Marquer comme terminée
                            </button>
                        </div>
                    </div>
                ` : ''}
            </div>
        `).join('');
    }

    // Marquer une course comme terminée
    window.completeRide = async function(id) {
        if (!confirm('Êtes-vous sûr de vouloir marquer cette course comme terminée ?')) {
            return;
        }

        try {
            const response = await fetch(`/driver/ride-request/${id}/complete`, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });

            if (!response.ok) {
                throw new Error('Erreur lors de la mise à jour');
            }

            const data = await response.json();
            showAlert('success', data.message || 'Course marquée comme terminée');
            loadRides();
        } catch (error) {
            showAlert('error', error.message || 'Erreur lors de la mise à jour de la course');
        }
    };

    // Fonctions utilitaires
    function getNotificationIcon(type) {
        switch (type) {
            case 'ride_request':
                return 'hand-paper';
            case 'ride_deleted':
                return 'trash';
            case 'ride_cancelled':
                return 'times-circle';
            case 'ride_confirmed':
                return 'check-circle';
            case 'ride_completed':
                return 'flag-checkered';
            default:
                return 'bell';
        }
    }

    function getNotificationTitle(type) {
        switch (type) {
            case 'ride_request':
                return 'Nouvelle demande';
            case 'ride_deleted':
                return 'Réservation supprimée';
            case 'ride_cancelled':
                return 'Réservation annulée';
            case 'ride_confirmed':
                return 'Réservation confirmée';
            case 'ride_completed':
                return 'Course terminée';
            default:
                return 'Notification';
        }
    }

    function getStatusLabel(status) {
        const labels = {
            'pending': 'En attente',
            'accepted': 'Acceptée',
            'rejected': 'Refusée',
            'cancelled': 'Annulée',
            'completed': 'Terminée'
        };
        return labels[status] || status;
    }

    // Événements pour les boutons de rafraîchissement
    document.getElementById('refresh-announcements').addEventListener('click', loadAnnouncements);
    document.getElementById('refresh-requests').addEventListener('click', loadPendingRequests);
    document.getElementById('refresh-notifications').addEventListener('click', loadNotifications);
    document.getElementById('refresh-reservations').addEventListener('click', loadRides);
    document.getElementById('mark-all-read').addEventListener('click', markAllAsRead);

    // Événements pour les boutons de filtre
    document.querySelectorAll('.btn-group button[data-filter]').forEach(button => {
        button.addEventListener('click', function() {
            filterRides(this.getAttribute('data-filter'));
        });
    });

    // Initialisation
    loadAnnouncements();
    loadPendingRequests();
    loadNotifications();
    loadRides();

    // Mettre à jour les données toutes les 30 secondes
    setInterval(() => {
        loadAnnouncements();
        loadPendingRequests();
        loadNotifications();
        loadRides();
    }, 30000);

    // Écouter les événements de notification en temps réel
    if (typeof Echo !== 'undefined') {
        Echo.private(`App.Models.User.${userId}`)
            .notification((notification) => {
                loadNotifications();
                loadPendingRequests();
                loadRides();

                // Afficher une notification système
                if (Notification.permission === 'granted') {
                    const title = getNotificationTitle(notification.type);
                    const options = {
                        body: notification.message,
                        icon: '/images/logo.png'
                    };
                    new Notification(title, options);
                }
            });
    }
});
</script>
@endsection

