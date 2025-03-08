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

// Fonction pour obtenir le libellé du statut
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

// Fonction pour marquer une course comme terminée et envoyer un SMS
window.completeRide = async function(id) {
    if (!confirm('Êtes-vous sûr de vouloir marquer cette course comme terminée ?')) {
        return;
    }

    try {
        // Numéro de téléphone spécifique pour l'envoi du SMS
        const phoneNumber = '0701237397'; // Remplacez par le numéro que vous souhaitez

        const response = await fetch(`/driver/ride-request/${id}/complete`, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                phone_number: phoneNumber
            })
        });

        if (!response.ok) {
            const data = await response.json();
            throw new Error(data.message || 'Erreur lors de la mise à jour');
        }

        const data = await response.json();
        showAlert('success', data.message || 'Course marquée comme terminée et SMS envoyé');
        loadRides();
    } catch (error) {
        console.error('Erreur:', error);
        showAlert('error', error.message || 'Erreur lors de la mise à jour de la course');
    }
};

// Fonction utilitaire pour afficher les alertes (à adapter selon votre implémentation)
function showAlert(type, message) {
    // Implémentation de base - à adapter selon votre système d'alertes
    alert(`${type.toUpperCase()}: ${message}`);
}

// Fonction pour charger les courses (à adapter selon votre implémentation)
function loadRides() {
    // Implémentation à adapter selon votre code existant
    console.log('Rechargement des courses...');
    // Exemple: fetch('/driver/rides').then(...)
}
