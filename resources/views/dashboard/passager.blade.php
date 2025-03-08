@extends('layouts.app')


@section('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>

.reviews-list {
    max-height: 500px;
    overflow-y: auto;
}

.review-item {
    border-bottom: 1px solid #e5e7eb;
    padding: 1rem 0;
}

.review-item:last-child {
    border-bottom: none;
}

.review-header {
    display: flex;
    justify-content: space-between;
    margin-bottom: 0.5rem;
}

.review-rating {
    color: #ffc107;
}

.review-date {
    color: #6b7280;
    font-size: 0.875rem;
}

.review-comment {
    color: #4b5563;
}

.rating-display {
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    font-weight: 500;
}
.rating-stars {
    display: flex;
    justify-content: center;
    gap: 10px;
}

.rating-star {
    cursor: pointer;
    color: #ccc;
    transition: color 0.2s;
}

.rating-star.active, .rating-star:hover {
    color: #ffc107;
}

.rating-text {
    font-weight: 500;
    min-height: 24px;
}
   #map {
        height: 400px;
        width: 100%;
        border-radius: 8px;
        margin-bottom: 20px;
    }
    .driver-card {
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        overflow: hidden;
        margin-bottom: 20px;
        transition: all 0.2s ease;
    }
    .driver-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }
    .driver-header {
        padding: 16px;
        background-color: #f8fafc;
        border-bottom: 1px solid #e5e7eb;
    }
    .driver-profile {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .driver-avatar {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        object-fit: cover;
    }
    .driver-info h3 {
        margin: 0;
        font-size: 1.1rem;
        color: #1f2937;
    }
    .driver-info p {
        margin: 0;
        color: #6b7280;
    }
    .driver-body {
        padding: 16px;
    }
    .location-info {
        display: flex;
        align-items: flex-start;
        gap: 8px;
        margin-bottom: 12px;
    }
    .location-icon {
        color: #059669;
    }
    .location-details {
        flex: 1;
    }
    .location-name {
        font-weight: 500;
        color: #1f2937;
    }
    .driver-note {
        background-color: #f3f4f6;
        padding: 12px;
        border-radius: 8px;
        margin-top: 12px;
        font-size: 0.9rem;
        color: #4b5563;
    }
    .driver-footer {
        padding: 16px;
        background-color: #f8fafc;
        border-top: 1px solid #e5e7eb;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .booking-map {
        height: 300px;
        border-radius: 8px;
        margin-bottom: 15px;
    }
    .status-badge {
        padding: 4px 12px;
        border-radius: 9999px;
        font-size: 0.875rem;
        font-weight: 500;
    }
    .status-pending {
        background-color: #fef3c7;
        color: #92400e;
    }
    .status-accepted {
        background-color: #d1fae5;
        color: #065f46;
    }
    .status-rejected {
        background-color: #fee2e2;
        color: #991b1b;
    }
    .empty-state {
        text-align: center;
        padding: 48px 24px;
        background-color: #f9fafb;
        border-radius: 12px;
    }
    .empty-state i {
        font-size: 48px;
        color: #9ca3af;
        margin-bottom: 16px;
    }
    .location-search-container {
        position: relative;
        margin-bottom: 1rem;
    }

    .location-search-input {
        width: 100%;
        padding: 0.5rem 2.5rem 0.5rem 1rem;
        border: 1px solid #e2e8f0;
        border-radius: 0.375rem;
        font-size: 0.875rem;
    }

    .location-search-input:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    .location-search-button {
        position: absolute;
        right: 0.5rem;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        color: #4b5563;
        cursor: pointer;
        padding: 0.25rem;
    }

    .location-search-button:hover {
        color: #1f2937;
    }

    .filters-container {
        background: white;
        padding: 1rem;
        border-radius: 0.5rem;
        margin-bottom: 1rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .filter-group {
        display: flex;
        gap: 1rem;
        align-items: center;
        margin-bottom: 1rem;
    }

    .filter-group:last-child {
        margin-bottom: 0;
    }

    .filter-label {
        font-weight: 500;
        min-width: 100px;
    }

    .filter-select {
        flex: 1;
        padding: 0.5rem;
        border: 1px solid #e5e7eb;
        border-radius: 0.375rem;
        background-color: white;
    }

    .filter-input {
        flex: 1;
        padding: 0.5rem;
        border: 1px solid #e5e7eb;
        border-radius: 0.375rem;
    }

    .filter-button {
        padding: 0.5rem 1rem;
        background-color: #3b82f6;
        color: white;
        border: none;
        border-radius: 0.375rem;
        cursor: pointer;
        transition: background-color 0.2s;
    }

    .filter-button:hover {
        background-color: #2563eb;
    }

    .filter-button.clear {
        background-color: #ef4444;
    }

    .filter-button.clear:hover {
        background-color: #dc2626;
    }
</style>
@endsection

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h2 class="card-title h5 mb-0">Chauffeurs disponibles</h2>
                        <div class="d-flex gap-3">
                            <button class="btn btn-outline-primary btn-sm" onclick="showMyRequests()">
                                <i class="fas fa-list me-2"></i>Mes réservations
                            </button>
                            <div class="text-muted small">
                                <i class="fas fa-sync-alt me-1"></i>
                                Mise à jour automatique
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filtres -->
                    <div class="filters-container">
                        <div class="filter-group">
                            <span class="filter-label">Pays</span>
                            <select id="countryFilter" class="filter-select" onchange="applyFilters()">
                                <option value="all">Tous les pays</option>
                            </select>
                        </div>
                        <div class="filter-group">
                            <span class="filter-label">Ville</span>
                            <input type="text" id="cityFilter" class="filter-input"
                                placeholder="Filtrer par ville..."
                                onkeyup="debounce(applyFilters, 500)()">
                            <button onclick="clearFilters()" class="filter-button clear">
                                <i class="fas fa-times me-2"></i>Effacer
                            </button>
                        </div>
                    </div>

                    <!-- Carte -->
                    <div id="map"></div>

                    <!-- Liste des chauffeurs -->
                    <div id="drivers-list">
                        <!-- Les chauffeurs seront injectés ici -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de réservation -->
<div class="modal fade" id="bookingModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Réserver une course</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="driver-profile mb-4">
                    <img id="bookingDriverImage" src="/placeholder.svg" alt="" class="driver-avatar">
                    <div class="driver-info">
                        <h3 id="bookingDriverName"></h3>
                        <p id="bookingDriverPhone"></p>
                    </div>
                </div>

                <div class="location-search-container">
                    <input type="text"
                           id="pickupAddress"
                           class="location-search-input"
                           placeholder="Entrez l'adresse de départ"
                           autocomplete="off">
                    <button type="button"
                            class="location-search-button"
                            onclick="searchLocation('pickup')">
                        <i class="fas fa-search"></i>
                    </button>
                </div>

                <div class="location-search-container">
                    <input type="text"
                           id="destinationAddress"
                           class="location-search-input"
                           placeholder="Entrez l'adresse de destination"
                           autocomplete="off">
                    <button type="button"
                            class="location-search-button"
                            onclick="searchLocation('destination')">
                        <i class="fas fa-search"></i>
                    </button>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Date du voyage</label>
                        <input type="date" id="scheduledDate" class="form-control"
                            min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Heure du voyage</label>
                        <input type="time" id="scheduledTime" class="form-control">
                    </div>
                </div>

                <div id="bookingMap" class="booking-map"></div>

                <div class="mb-3">
                    <label class="form-label">Note pour le chauffeur (optionnel)</label>
                    <textarea id="bookingNote" class="form-control" rows="3"
                        placeholder="Informations supplémentaires pour le chauffeur..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-primary" onclick="submitBooking()">
                    <i class="fas fa-check me-2"></i>Confirmer la réservation
                </button>
                {{--  Open Channnele  --}}
                -<div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="button" class="btn btn-primary" onclick="submitBooking()">
                        <i class="fas fa-check me-2"></i>Confirmer la reservation
                    </button>


                    {{-- This button will only be shown for existing rides --}}
                    @if(isset($ride) && $ride->id)
                    <a href="{{ route('chat.ride', ['rideId' => $ride->id]) }}" class="btn btn-info">
                        <i class="fas fa-comment me-2"></i>Envoyer un message
                    </a>
                    @endif

                    {{-- @if(isset($)) --}}
                </div>

            </div>
        </div>
    </div>
</div>

<!-- Modal des réservations -->
<div class="modal fade" id="requestsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Mes réservations</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="requestsList">
                    <!-- Les réservations seront injectées ici -->
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    // Système de notation
document.addEventListener('DOMContentLoaded', function() {
    const ratingStars = document.querySelectorAll('.rating-star');
    const ratingText = document.getElementById('ratingText');
    const ratingValue = document.getElementById('ratingValue');
    const submitRating = document.getElementById('submitRating');
    const ratingModal = new bootstrap.Modal(document.getElementById('ratingModal'));

    // Textes correspondant aux notes
    const ratingLabels = {
        1: 'Très mauvais',
        2: 'Mauvais',
        3: 'Correct',
        4: 'Bon',
        5: 'Excellent'
    };

    // Gérer le survol et la sélection des étoiles
    ratingStars.forEach(star => {
        // Survol
        star.addEventListener('mouseenter', () => {
            const rating = parseInt(star.dataset.rating);
            updateStars(rating, false);
            ratingText.textContent = ratingLabels[rating];
        });

        // Sortie du survol
        star.addEventListener('mouseleave', () => {
            const currentRating = parseInt(ratingValue.value);
            updateStars(currentRating, false);
            ratingText.textContent = currentRating > 0 ? ratingLabels[currentRating] : 'Sélectionnez une note';
        });

        // Clic
        star.addEventListener('click', () => {
            const rating = parseInt(star.dataset.rating);
            ratingValue.value = rating;
            updateStars(rating, true);
            ratingText.textContent = ratingLabels[rating];
            submitRating.disabled = false;
        });
    });

    // Mettre à jour l'affichage des étoiles
    function updateStars(rating, permanent = false) {
        ratingStars.forEach(star => {
            const starRating = parseInt(star.dataset.rating);

            if (permanent) {
                // Pour une sélection permanente
                if (starRating <= rating) {
                    star.classList.remove('far');
                    star.classList.add('fas', 'active');
                } else {
                    star.classList.remove('fas', 'active');
                    star.classList.add('far');
                }
            } else {
                // Pour le survol temporaire
                if (starRating <= rating) {
                    star.classList.remove('far');
                    star.classList.add('fas');
                } else {
                    star.classList.remove('fas');
                    star.classList.add('far');

                    // Conserver les étoiles sélectionnées
                    const selectedRating = parseInt(ratingValue.value);
                    if (selectedRating > 0 && starRating <= selectedRating) {
                        star.classList.remove('far');
                        star.classList.add('fas', 'active');
                    }
                }
            }
        });
    }

    // Soumettre la notation
    submitRating.addEventListener('click', async function() {
        const rating = parseInt(ratingValue.value);
        const comment = document.getElementById('ratingComment').value;
        const rideId = document.getElementById('ratingRideId').value;

        if (rating < 1 || rating > 5) return;

        // Désactiver le bouton pendant la soumission
        this.disabled = true;
        this.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Envoi en cours...';

        try {
            const response = await fetch('/passenger/submit-rating', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    ride_id: rideId,
                    rating: rating,
                    comment: comment
                })
            });

            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.error || 'Erreur lors de la soumission de la notation');
            }

            // Fermer le modal et réinitialiser
            ratingModal.hide();
            resetRatingModal();

            // Afficher un message de succès
            alert('Merci pour votre évaluation !');

        } catch (error) {
            alert(error.message);
        } finally {
            // Réactiver le bouton
            this.disabled = false;
            this.innerHTML = 'Soumettre l\'évaluation';
        }
    });

    // Réinitialiser le modal
    function resetRatingModal() {
        ratingValue.value = 0;
        document.getElementById('ratingComment').value = '';
        document.getElementById('ratingRideId').value = '';
        ratingText.textContent = 'Sélectionnez une note';
        submitRating.disabled = true;

        // Réinitialiser les étoiles
        ratingStars.forEach(star => {
            star.classList.remove('fas', 'active');
            star.classList.add('far');
        });
    }

    // Vérifier périodiquement s'il y a des courses terminées à évaluer
    function checkForCompletedRides() {
        fetch('/passenger/check-completed-rides')
            .then(response => response.json())
            .then(data => {
                if (data.success && data.ride) {
                    // Préparer et afficher le modal de notation
                    document.getElementById('ratingDriverName').textContent = data.ride.driver_name;
                    document.getElementById('ratingDriverImage').src = data.ride.driver_image || '/images/default-avatar.png';
                    document.getElementById('ratingRideId').value = data.ride.id;

                    // Afficher le modal
                    ratingModal.show();
                }
            })
            .catch(error => console.error('Erreur lors de la vérification des courses terminées:', error));
    }

    // Vérifier immédiatement au chargement de la page
    checkForCompletedRides();

    // Puis vérifier toutes les 30 secondes
    setInterval(checkForCompletedRides, 30000);
});
document.addEventListener('DOMContentLoaded', function() {
    // Variables globales
    let map = null;
    let markers = [];
    let bookingMap = null;
    let bookingMarkers = {
        pickup: null,
        destination: null,
        driver: null
    };
    let currentDriver = null;
    let bookingModal = new bootstrap.Modal(document.getElementById('bookingModal'));
    let requestsModal = new bootstrap.Modal(document.getElementById('requestsModal'));

    // Initialiser la carte principale
    function initMap() {
        map = L.map('map').setView([31.7917, -7.0926], 13);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);
    }

    // Initialiser la carte de réservation
    function initBookingMap() {
        if (!bookingMap) {
            bookingMap = L.map('bookingMap').setView([31.7917, -7.0926], 13);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(bookingMap);
        }
    }

    // Fonction debounce pour éviter trop d'appels lors de la saisie
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    // Charger les chauffeurs disponibles
    async function loadDrivers() {
        const country = document.getElementById('countryFilter').value;
        const city = document.getElementById('cityFilter').value.trim();
        try {
            const response = await fetch(`/passenger/available-drivers?country=${country}&city=${encodeURIComponent(city)}`);
            const data = await response.json();

            if (!data.success) {
                throw new Error(data.error || 'Erreur lors du chargement des chauffeurs');
            }

            // Mettre à jour la carte et la liste
            updateMap(data.announcements);
            updateDriversList(data.announcements);
        } catch (error) {
            console.error('Erreur:', error);
            document.getElementById('drivers-list').innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    ${error.message}
                </div>
            `;
        }
    }


    // Créer le popup pour la carte
    function createDriverPopup(announcement) {
        return `
            <div class="driver-popup">
                <div class="d-flex align-items-center mb-2">
                    <img src="${announcement.driver.profile_image_url}"
                        alt="${announcement.driver.name}"
                        class="rounded-circle me-2"
                        style="width: 40px; height: 40px; object-fit: cover;">
                    <div>
                        <div class="fw-bold">${announcement.driver.name}</div>
                        <div class="small text-muted">${announcement.driver.phone}</div>
                    </div>
                </div>
                <div class="small mb-2">${announcement.location_name}</div>
                <button onclick="showBooking(${JSON.stringify(announcement).replace(/"/g, '&quot;')})"
                    class="btn btn-primary btn-sm w-100">
                    <i class="fas fa-car me-1"></i>Réserver
                </button>

            </div>


        `;
    }

    // Créer une carte de chauffeur
    function createDriverCard(announcement) {
        return `
            <div class="driver-card">
                <div class="driver-header">
                    <div class="driver-profile">
                        <img src="${announcement.driver.profile_image_url}" alt="${announcement.driver.name}" class="driver-avatar">
                        <div class="driver-info">
                            <h3>${announcement.driver.name}</h3>
                            <p><i class="fas fa-phone me-1"></i>${announcement.driver.phone}</p>
                        </div>
                    </div>
                </div>
                <div class="driver-body">
                    <div class="location-info">
                        <div class="location-icon">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div class="location-details">
                            <div class="location-name">${announcement.location_name}</div>
                            <div class="text-muted">
                                <i class="fas fa-map-pin me-1"></i>${announcement.city}, ${announcement.country}
                            </div>
                        </div>
                    </div>
                    ${announcement.note ? `
                        <div class="driver-note">
                            <i class="fas fa-info-circle me-2"></i>${announcement.note}
                        </div>
                    ` : ''}
                </div>
                <div class="driver-footer">
            <div class="text-muted small">
                <i class="far fa-clock me-1"></i>
                Disponible depuis ${new Date(announcement.created_at).toLocaleString()}
            </div>
            <div class="d-flex gap-2">
                <button onclick="showReviews(${announcement.driver.id}, '${announcement.driver.name}', '${announcement.driver.profile_image_url}')"
                    class="btn btn-outline-primary">
                    <i class="fas fa-star me-2"></i>Avis
                </button>
                <button onclick="showBooking(${JSON.stringify(announcement).replace(/"/g, '&quot;')})"
                    class="btn btn-primary">
                    <i class="fas fa-car me-2"></i>Réserver
                </button>
            </div>
        </div>


                    <button onclick="showBooking(${JSON.stringify(announcement).replace(/"/g, '&quot;')})"
                        class="btn btn-primary">
                        <i class="fas fa-car me-2"></i>Réserver
                    </button>
                </div>
            </div>
        `;
    }

    // Afficher le modal de réservation
    window.showBooking = function(announcement) {
        currentDriver = announcement;

        // Mettre à jour les informations du chauffeur
        document.getElementById('bookingDriverImage').src = announcement.driver.profile_image_url;
        document.getElementById('bookingDriverName').textContent = announcement.driver.name;
        document.getElementById('bookingDriverPhone').textContent = announcement.driver.phone;

        // Réinitialiser les champs
        document.getElementById('pickupAddress').value = '';
        document.getElementById('destinationAddress').value = '';
        document.getElementById('bookingNote').value = '';
        document.getElementById('scheduledDate').value = '';
        document.getElementById('scheduledTime').value = '';

        // Afficher le modal
        bookingModal.show();

        // Initialiser la carte après l'affichage du modal
        setTimeout(() => {
            initBookingMap();
            bookingMap.invalidateSize();

            // Ajouter le marqueur du chauffeur
            if (bookingMarkers.driver) {
                bookingMap.removeLayer(bookingMarkers.driver);
            }
            bookingMarkers.driver = L.marker([announcement.latitude, announcement.longitude], {
                icon: L.divIcon({
                    html: '<i class="fas fa-car text-primary"></i>',
                    className: 'driver-marker',
                    iconSize: [20, 20]
                })
            }).addTo(bookingMap);

            bookingMap.setView([announcement.latitude, announcement.longitude], 13);
        }, 300);
    }

    // Utiliser la position actuelle
    window.useCurrentLocation = function() {
        if (!navigator.geolocation) {
            alert("La géolocalisation n'est pas supportée par votre navigateur.");
            return;
        }

        navigator.geolocation.getCurrentPosition(
            function(position) {
                const { latitude, longitude } = position.coords;

                // Mettre à jour le marqueur de départ
                if (bookingMarkers.pickup) {
                    bookingMarkers.pickup.setLatLng([latitude, longitude]);
                } else {
                    bookingMarkers.pickup = L.marker([latitude, longitude], {
                        draggable: true,
                        icon: L.divIcon({
                            html: '<i class="fas fa-map-marker-alt text-success"></i>',
                            className: 'pickup-marker',
                            iconSize: [20, 20]
                        })
                    }).addTo(bookingMap);
                }

                // Centrer la carte
                bookingMap.setView([latitude, longitude], 15);

                // Obtenir l'adresse
                fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${latitude}&lon=${longitude}`)
                    .then(response => response.json())
                    .then(data => {
                        document.getElementById('pickupAddress').value = data.display_name;
                    });
            },
            function(error) {
                alert("Erreur lors de la récupération de votre position.");
            }
        );
    }

    // Rechercher une adresse
    window.searchLocation = async function(type) {
        const input = type === 'destination' ?
            document.getElementById('destinationAddress') :
            document.getElementById('pickupAddress');

        const address = input.value;
        if (!address) return;

        try {
            const response = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(address)}&countrycodes=ma`);
            const data = await response.json();

            if (data.length > 0) {
                const { lat, lon } = data[0];

                // Mettre à jour le marqueur
                const markerKey = type === 'destination' ? 'destination' : 'pickup';
                const markerIcon = type === 'destination' ?
                    '<i class="fas fa-flag-checkered text-danger"></i>' :
                    '<i class="fas fa-map-marker-alt text-success"></i>';

                if (bookingMarkers[markerKey]) {
                    bookingMarkers[markerKey].setLatLng([lat, lon]);
                } else {
                    bookingMarkers[markerKey] = L.marker([lat, lon], {
                        draggable: true,
                        icon: L.divIcon({
                            html: markerIcon,
                            className: `${markerKey}-marker`,
                            iconSize: [20, 20]
                        })
                    }).addTo(bookingMap);

                    // Mettre à jour l'adresse lors du drag and drop
                    bookingMarkers[markerKey].on('dragend', async function(e) {
                        const pos = e.target.getLatLng();
                        try {
                            const response = await fetch(
                                `https://nominatim.openstreetmap.org/reverse?format=json&lat=${pos.lat}&lon=${pos.lng}`
                            );
                            const data = await response.json();
                            input.value = data.display_name;
                        } catch (error) {
                            console.error('Erreur lors de la récupération de l\'adresse:', error);
                        }
                    });
                }

                // Mettre à jour l'adresse
                input.value = data[0].display_name;

                // Centrer la carte
                bookingMap.setView([lat, lon], 15);
            } else {
                alert("Adresse non trouvée");
            }
        } catch (error) {
            console.error('Erreur lors de la recherche:', error);
            alert("Erreur lors de la recherche de l'adresse");
        }
    }

    // Soumettre la réservation
    window.submitBooking = async function() {
        if (!bookingMarkers.pickup || !bookingMarkers.destination) {
            alert("Veuillez sélectionner un point de départ et une destination");
            return;
        }

        const scheduledDate = document.getElementById('scheduledDate').value;
        const scheduledTime = document.getElementById('scheduledTime').value;
        const pickupAddress = document.getElementById('pickupAddress').value;
        const destinationAddress = document.getElementById('destinationAddress').value;

        if (!scheduledDate || !scheduledTime) {
            alert("Veuillez sélectionner une date et une heure pour le voyage");
            return;
        }

        if (!pickupAddress || !destinationAddress) {
            alert("Veuillez entrer les adresses de départ et de destination");
            return;
        }

        try {
            const response = await fetch('/passenger/ride-request', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    announcement_id: currentDriver.id,
                    pickup_lat: bookingMarkers.pickup.getLatLng().lat,
                    pickup_lng: bookingMarkers.pickup.getLatLng().lng,
                    pickup_address: pickupAddress,
                    destination_lat: bookingMarkers.destination.getLatLng().lat,
                    destination_lng: bookingMarkers.destination.getLatLng().lng,
                    destination_address: destinationAddress,
                    scheduled_date: scheduledDate,
                    scheduled_time: scheduledTime,
                    note: document.getElementById('bookingNote').value || ''
                })
            });

            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.error || "Erreur lors de l'envoi de la demande");
            }

            alert("Votre demande a été envoyée au chauffeur!");
            bookingModal.hide();
            loadDrivers();
        } catch (error) {
            alert(error.message);
        }
    }

    // Afficher les réservations
    window.showMyRequests = async function() {
        try {
            const response = await fetch('{{ route("ride.requests") }}');
            const requests = await response.json();

            const container = document.getElementById('requestsList');

            if (requests.length === 0) {
                container.innerHTML = `
                    <div class="empty-state">
                        <i class="fas fa-car"></i>
                        <h3>Aucune réservation</h3>
                        <p>Vous n'avez pas encore effectué de réservation.</p>
                    </div>
                `;
            } else {
                container.innerHTML = requests.map(request => {
                    const canCancel = request.status === 'pending' ||
                        (request.status === 'accepted' && request.can_be_cancelled);

                    function getActionButtons(request) {
                        if (request.status === 'accepted') {
                            if (!request.passenger_confirmation_at) {
                                return `
                                    <button onclick="confirmRequest(${request.id})" class="btn btn-success btn-sm">
                                        <i class="fas fa-check me-1"></i>Confirmer
                                    </button>
                                `;
                            } else if (request.can_be_deleted) {
                                return `
                                    <button onclick="deleteRequest(${request.id})" class="btn btn-danger btn-sm">
                                        <i class="fas fa-trash me-1"></i>Supprimer
                                    </button>
                                `;
                            } else if (request.can_be_cancelled) {
                                const minutesLeft = request.minutes_until_departure;
                                return `
                                    <button onclick="cancelRequest(${request.id})" class="btn btn-warning btn-sm">
                                        <i class="fas fa-times me-1"></i>Annuler
                                    </button>
                                    <small class="text-muted ms-2">
                                        (${minutesLeft} minutes avant départ)
                                    </small>
                                `;
                            } else {
                                return `
                                    <span class="badge bg-warning text-dark">
                                        <i class="fas fa-lock me-1"></i>Annulation impossible (moins de 5 min)
                                    </span>
                                `;
                            }
                        }
                        return '';
                    }

                    return `
                        <div class="driver-card mb-3">
                            <div class="driver-header">
                                <div class="driver-profile">
                                    <img src="${request.driver.profile_image_url}" alt="" class="driver-avatar">
                                    <div class="driver-info">
                                        <h3>${request.driver.name}</h3>
                                        <p><i class="fas fa-phone me-1"></i>${request.driver.phone}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="driver-body">
                                <div class="d-flex justify-content-between mb-3">
                                    <div class="fw-bold">Statut:</div>
                                    <span class="status-badge status-${request.status}">
                                        ${getStatusLabel(request.status)}
                                        ${request.passenger_confirmation_at ? ' (Confirmé)' : ''}
                                    </span>
                                </div>
                                <div class="mb-3">
                                    <div class="fw-bold mb-1">Date et heure du voyage:</div>
                                    <div>
                                        <i class="far fa-calendar-alt me-1"></i>
                                        ${new Date(request.scheduled_at).toLocaleString()}
                                        ${request.status === 'accepted' ? `
                                            <div class="alert ${request.can_be_cancelled ? 'alert-info' : 'alert-warning'} mt-2">
                                                <i class="far fa-clock me-1"></i>
                                                Départ dans ${request.time_until_departure}
                                                ${!request.can_be_cancelled ? `
                                                    <br>
                                                    <small class="text-danger">
                                                        <i class="fas fa-exclamation-triangle me-1"></i>
                                                        L'annulation n'est plus possible 5 minutes avant le départ
                                                    </small>
                                                ` : ''}
                                            </div>
                                        ` : ''}
                                    </div>
                                </div>
                                <div class="location-info">
                                    <div class="location-icon">
                                        <i class="fas fa-map-marker-alt text-success"></i>
                                    </div>
                                    <div class="location-details">
                                        <div class="fw-bold">Départ</div>
                                        <div>${request.pickup_address}</div>
                                    </div>
                                </div>
                                <div class="location-info">
                                    <div class="location-icon">
                                        <i class="fas fa-flag-checkered text-danger"></i>
                                    </div>
                                    <div class="location-details">
                                        <div class="fw-bold">Destination</div>
                                        <div>${request.destination_address}</div>
                                    </div>
                                </div>
                                ${request.note ? `
                                    <div class="driver-note">
                                        <i class="fas fa-info-circle me-2"></i>${request.note}
                                    </div>
                                ` : ''}
                            </div>
                            <div class="driver-footer">
                                <div class="text-muted small">
                                    <i class="far fa-clock me-1"></i>
                                    Demande effectuée le ${new Date(request.created_at).toLocaleString()}
                                </div>
                                <div class="d-flex gap-2 align-items-center">
                                    ${getActionButtons(request)}
                                </div>
                            </div>
                        </div>
                    `;
                }).join('');
            }

            requestsModal.show();
        } catch (error) {
            alert("Erreur lors de la récupération de vos réservations");
        }
    }

    // Annuler une réservation
    async function cancelRequest(id) {
        if (!confirm('Êtes-vous sûr de vouloir annuler cette réservation ?')) {
            return;
        }

        try {
            const response = await fetch(`/passenger/ride-request/${id}/cancel`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });

            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.error || 'Erreur lors de l\'annulation');
            }

            alert('Réservation annulée avec succès !');
            showMyRequests(); // Rafraîchir la liste
        } catch (error) {
            alert(error.message);
        }
    }

    // Ajouter la fonction de confirmation
    window.confirmRequest = async function(id) {
        if (!confirm('Êtes-vous sûr de vouloir confirmer cette réservation ?')) {
            return;
        }

        try {
            const response = await fetch(`/passenger/ride-request/${id}/confirm`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });

            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.error || 'Erreur lors de la confirmation');
            }

            alert('Réservation confirmée avec succès !');
            showMyRequests(); // Rafraîchir la liste
        } catch (error) {
                        alert(error.message);
        }
    }

    // Ajoutons la fonction de suppression :
    window.deleteRequest = async function(id) {
        if (!confirm('Êtes-vous sûr de vouloir supprimer définitivement cette réservation ? Cette action est irréversible.')) {
            return;
        }

        try {
            const response = await fetch(`/passenger/ride-request/${id}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });

            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.error || 'Erreur lors de la suppression');
            }

            // Afficher un message de succès
            alert('Réservation supprimée avec succès');

            // Rafraîchir la liste des réservations
            showMyRequests();

        } catch (error) {
            alert(error.message);
        }
    }


    // Fonction utilitaire pour les statuts
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

    // Charger les pays disponibles
    async function loadAvailableCountries() {
        try {
            const response = await fetch('/driver/available-countries');
            const data = await response.json();

            if (!data.success) {
                throw new Error(data.error || 'Erreur lors du chargement des pays');
            }

            const countryFilter = document.getElementById('countryFilter');
            countryFilter.innerHTML = '<option value="all">Tous les pays</option>';

            data.countries.forEach(country => {
                const option = document.createElement('option');
                option.value = country;
                option.textContent = country;
                countryFilter.appendChild(option);
            });
        } catch (error) {
            console.error('Erreur:', error);
        }
    }

    // Appliquer les filtres
    window.applyFilters = async function() {
        const country = document.getElementById('countryFilter').value;
        const city = document.getElementById('cityFilter').value.trim();

        try {
            const response = await fetch(`/passenger/available-drivers?country=${country}&city=${encodeURIComponent(city)}`);
            const data = await response.json();

            if (!data.success) {
                throw new Error(data.error || 'Erreur lors du chargement des chauffeurs');
            }

            // Mettre à jour la carte et la liste
            updateMap(data.announcements);
            updateDriversList(data.announcements);
        } catch (error) {
            console.error('Erreur:', error);
            document.getElementById('drivers-list').innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    ${error.message}
                </div>
            `;
        }
    }

    // Effacer les filtres
    window.clearFilters = function() {
        document.getElementById('countryFilter').value = 'all';
        document.getElementById('cityFilter').value = '';
        applyFilters();
    }

    // Mettre à jour la carte
    function updateMap(announcements) {
        // Nettoyer les marqueurs existants
        markers.forEach(marker => map.removeLayer(marker));
        markers = [];

        if (announcements.length === 0) {
            map.setView([31.7917, -7.0926], 13);
            return;
        }

        const bounds = [];

        announcements.forEach(announcement => {
            const marker = L.marker([announcement.latitude, announcement.longitude])
                .bindPopup(createDriverPopup(announcement))
                .addTo(map);
            markers.push(marker);
            bounds.push([announcement.latitude, announcement.longitude]);
        });

        if (bounds.length > 0) {
            map.fitBounds(bounds);
        }
    }

    // Mettre à jour la liste des chauffeurs
//     function updateDriversList(announcements) {
//         const container = document.getElementById('drivers-list');

//         if (announcements.length === 0) {
//             container.innerHTML = `
//                 <div class="empty-state">
//                     <i class="fas fa-car"></i>
//                     <h3>Aucun chauffeur disponible</h3>
//                     <p>Il n'y a pas de chauffeurs disponibles pour les critères sélectionnés.</p>
//                 </div>
//             `;
//             return;
//         }

//         container.innerHTML = announcements.map(announcement => `
//             <div class="driver-card">
//                 <div class="driver-header">
//                     <div class="driver-profile">
//                         <img src="${announcement.driver.profile_image_url}" alt="${announcement.driver.name}" class="driver-avatar">
//                         <div class="driver-info">
//                             <h3>${announcement.driver.name}</h3>
//                             <p><i class="fas fa-phone me-1"></i>${announcement.driver.phone}</p>
//                         </div>
//                     </div>
//                 </div>
//                 <div class="driver-body">
//                     <div class="location-info">
//                         <div class="location-icon">
//                             <i class="fas fa-map-marker-alt"></i>
//                         </div>
//                         <div class="location-details">
//                             <div class="location-name">${announcement.location_name}</div>
//                             <div class="text-muted">
//                                 <i class="fas fa-map-pin me-1"></i>${announcement.city}, ${announcement.country}
//                             </div>
//                         </div>
//                     </div>
//                     ${announcement.note ? `
//                         <div class="driver-note">
//                             <i class="fas fa-info-circle me-2"></i>${announcement.note}
//                         </div>
//                     ` : ''}
//                 </div>
//                 <div class="driver-footer">
//                     <div class="text-muted small">
//                         <i class="far fa-clock me-1"></i>
//                         Disponible depuis ${new Date(announcement.created_at).toLocaleString()}
//                     </div>
//                     <button onclick="showBooking(${JSON.stringify(announcement).replace(/"/g, '&quot;')})"
//                         class="btn btn-primary">
//                         <i class="fas fa-car me-2"></i>Réserver
//                     </button>

//                     <button onclick="showReviews(driverId)" class="btn btn-outline-primary">
//     <i class="fas fa-star me-2"></i>Voir les avis
// </button>
//                 </div>
//             </div>
//         `).join('');
//     }


// Mettre à jour la liste des chauffeurs
function updateDriversList(announcements) {
  const container = document.getElementById("drivers-list")

  if (announcements.length === 0) {
    container.innerHTML = `
            <div class="empty-state">
                <i class="fas fa-car"></i>
                <h3>Aucun chauffeur disponible</h3>
                <p>Il n'y a pas de chauffeurs disponibles pour les critères sélectionnés.</p>
            </div>
        `
    return
  }

  container.innerHTML = announcements
    .map(
      (announcement) => `
        <div class="driver-card">
            <div class="driver-header">
                <div class="driver-profile">
                    <img src="${announcement.driver.profile_image_url}" alt="${announcement.driver.name}" class="driver-avatar">
                    <div class="driver-info">
                        <h3>${announcement.driver.name}</h3>
                        <p><i class="fas fa-phone me-1"></i>${announcement.driver.phone}</p>
                    </div>
                </div>
            </div>
            <div class="driver-body">
                <div class="location-info">
                    <div class="location-icon">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <div class="location-details">
                        <div class="location-name">${announcement.location_name}</div>
                        <div class="text-muted">
                            <i class="fas fa-map-pin me-1"></i>${announcement.city}, ${announcement.country}
                        </div>
                    </div>
                </div>
                ${
                  announcement.note
                    ? `
                    <div class="driver-note">
                        <i class="fas fa-info-circle me-2"></i>${announcement.note}
                    </div>
                `
                    : ""
                }
            </div>
            <div class="driver-footer">
                <div class="text-muted small">
                    <i class="far fa-clock me-1"></i>
                    Disponible depuis ${new Date(announcement.created_at).toLocaleString()}
                </div>
                <div class="d-flex gap-2">
                    <button onclick="showReviews(${announcement.driver.id}, '${announcement.driver.name}', '${announcement.driver.profile_image_url}')"
                        class="btn btn-outline-primary">
                        <i class="fas fa-star me-2"></i>Avis
                    </button>
                    <button onclick="showBooking(${JSON.stringify(announcement).replace(/"/g, "&quot;")})"
                        class="btn btn-primary">
                        <i class="fas fa-car me-2"></i>Réserver
                    </button>
                </div>
            </div>
        </div>
    `,
    )
    .join("")
}



    // Initialisation
    loadAvailableCountries();
    initMap();
    applyFilters();

    // Mise à jour automatique toutes les 30 secondes
    setInterval(applyFilters, 30000);
});


// Créer le popup pour la carte
function createDriverPopup(announcement) {
  return `
        <div class="driver-popup">
            <div class="d-flex align-items-center mb-2">
                <img src="${announcement.driver.profile_image_url}"
                    alt="${announcement.driver.name}"
                    class="rounded-circle me-2"
                    style="width: 40px; height: 40px; object-fit: cover;">
                <div>
                    <div class="fw-bold">${announcement.driver.name}</div>
                    <div class="small text-muted">${announcement.driver.phone}</div>
                </div>
            </div>
            <div class="small mb-2">${announcement.location_name}</div>
            <div class="d-flex gap-2">
                <button onclick="showReviews(${announcement.driver.id}, '${announcement.driver.name}', '${announcement.driver.profile_image_url}')"
                    class="btn btn-outline-primary btn-sm w-50">
                    <i class="fas fa-star me-1"></i>Avis
                </button>
                <button onclick="showBooking(${JSON.stringify(announcement).replace(/"/g, "&quot;")})"
                    class="btn btn-primary btn-sm w-50">
                    <i class="fas fa-car me-1"></i>Réserver
                </button>
            </div>
        </div>
    `
}



// Initialiser le modal des avis
const reviewsModal = new bootstrap.Modal(document.getElementById('reviewsModal'));



// Fonction pour afficher les avis
window.showReviews = async function(driverId, driverName, driverImage) {
    try {
        // Mettre à jour les informations du chauffeur dans le modal
        document.getElementById('reviewDriverName').textContent = driverName;
        document.getElementById('reviewDriverImage').src = driverImage || '/images/default-avatar.png';

        // Afficher le modal pendant le chargement
        reviewsModal.show();

        // Afficher un indicateur de chargement
        document.getElementById('reviewsList').innerHTML = `
            <div class="text-center py-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Chargement...</span>
                </div>
                <p class="mt-2">Chargement des avis...</p>
            </div>
        `;

        // Charger les avis
        const response = await fetch(`/driver/${driverId}/reviews`);
        const data = await response.json();

        if (!response.ok) {
            throw new Error(data.message || 'Erreur lors du chargement des avis');
        }

        // Mettre à jour la note moyenne
        document.getElementById('reviewDriverRating').textContent =
            data.average_rating ? data.average_rating.toFixed(1) : '0.0';
        document.getElementById('reviewCount').textContent =
            `(${data.reviews.length} avis)`;

        // Afficher les avis
        if (data.reviews.length === 0) {
            document.getElementById('reviewsList').innerHTML = `
                <div class="text-center py-4">
                    <i class="fas fa-comment-slash fa-2x text-muted mb-3"></i>
                    <p class="text-muted">Aucun avis pour le moment</p>
                </div>
            `;
            return;
        }

        document.getElementById('reviewsList').innerHTML = data.reviews.map(review => `
            <div class="review-item">
                <div class="review-header">
                    <div class="review-rating">
                        ${Array(5).fill(0).map((_, i) => `
                            <i class="fa${i < review.rating ? 's' : 'r'} fa-star"></i>
                        `).join('')}
                    </div>
                    <div class="review-date">
                        ${new Date(review.created_at).toLocaleDateString()}
                    </div>
                </div>
                ${review.comment ? `
                    <div class="review-comment">
                        ${review.comment}
                    </div>
                ` : ''}
            </div>
        `).join('');

    } catch (error) {
        console.error('Erreur:', error);
        document.getElementById('reviewsList').innerHTML = `
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle me-2"></i>
                ${error.message}
            </div>
        `;
    }
}
</script>
@endsection



@yield('content2')


<!-- Modal de notation -->
<div class="modal fade" id="ratingModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Évaluer votre course</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-4">
                    <img id="ratingDriverImage" src="/images/default-avatar.png" alt="Photo du chauffeur"
                         class="rounded-circle mb-3" style="width: 80px; height: 80px; object-fit: cover;">
                    <h5 id="ratingDriverName">Nom du chauffeur</h5>
                </div>

                <div class="text-center mb-4">
                    <p>Comment s'est passé votre trajet ?</p>
                    <div class="rating-stars">
                        <i class="far fa-star fa-2x rating-star" data-rating="1"></i>
                        <i class="far fa-star fa-2x rating-star" data-rating="2"></i>
                        <i class="far fa-star fa-2x rating-star" data-rating="3"></i>
                        <i class="far fa-star fa-2x rating-star" data-rating="4"></i>
                        <i class="far fa-star fa-2x rating-star" data-rating="5"></i>
                    </div>
                    <div class="rating-text mt-2" id="ratingText">Sélectionnez une note</div>
                </div>

                <div class="mb-3">
                    <label for="ratingComment" class="form-label">Commentaire (optionnel)</label>
                    <textarea id="ratingComment" class="form-control" rows="3"
                              placeholder="Partagez votre expérience..."></textarea>
                </div>

                <input type="hidden" id="ratingRideId" value="">
                <input type="hidden" id="ratingValue" value="0">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Plus tard</button>
                <button type="button" class="btn btn-primary" id="submitRating" disabled>
                    Soumettre l'évaluation
                </button>
            </div>
        </div>
    </div>
</div>

{{--  --}}


<!-- Modal des avis -->
<div class="modal fade" id="reviewsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Avis sur le chauffeur</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="driver-profile mb-4">
                    <img id="reviewDriverImage" src="/images/default-avatar.png" alt="" class="driver-avatar">
                    <div class="driver-info">
                        <h3 id="reviewDriverName"></h3>
                        <div class="d-flex align-items-center gap-2">
                            <div class="rating-display">
                                <i class="fas fa-star text-warning"></i>
                                <span id="reviewDriverRating">0.0</span>
                            </div>
                            <span class="text-muted" id="reviewCount">(0 avis)</span>
                        </div>
                    </div>
                </div>

                <div id="reviewsList" class="reviews-list">
                    <!-- Les avis seront injectés ici -->
                </div>
            </div>
        </div>
    </div>
</div>
