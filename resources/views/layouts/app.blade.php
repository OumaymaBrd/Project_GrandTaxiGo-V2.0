<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">

    <style>
        .navbar-nav .nav-item.dropdown:hover .dropdown-menu {
            display: block;
        }
        .announcement-preview {
            max-width: 350px;
            white-space: normal;
        }
        .announcement-item {
            padding: 10px 15px;
            border-bottom: 1px solid #eee;
        }
        .announcement-item:last-child {
            border-bottom: none;
        }
        .announcement-location {
            font-size: 0.9rem;
            color: #666;
        }
        .announcement-time {
            font-size: 0.8rem;
            color: #888;
        }
        .status-dot {
            display: inline-block;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            margin-right: 5px;
        }
        .status-active {
            background-color: #10b981;
        }
        .status-inactive {
            background-color: #ef4444;
        }
        .dropdown-menu {
            max-height: 400px;
            overflow-y: auto;
        }
        .notification-badge {
            position: absolute;
            top: 0;
            right: 0;
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
            line-height: 1;
            border-radius: 999px;
            background-color: #ef4444;
            color: white;
            transform: translate(50%, -50%);
        }
    </style>

    @yield('styles')
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="{{ url('/') }}">
                {{ config('app.name', 'Laravel') }}
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">

                <ul class="navbar-nav ms-auto">
                    @guest
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                        </li>
                    @else
                        <div class="d-flex align-items-center">
                            @if(auth()->user()->isChauffeur())
                                <div class="nav-item dropdown me-3">
                                    <a class="nav-link dropdown-toggle position-relative" href="#" id="announcementsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fas fa-bullhorn me-1"></i> Mes Annonces
                                        @if(Auth::user()->activeAnnouncements()->count() > 0)
                                            <span class="notification-badge">
                                                {{ Auth::user()->activeAnnouncements()->count() }}
                                            </span>
                                        @endif
                                    </a>
                                    <ul class="dropdown-menu dropdown-menu-end announcement-preview" aria-labelledby="announcementsDropdown">
                                        @forelse(Auth::user()->announcements()->latest()->take(5)->get() as $announcement)
                                            <li class="announcement-item">
                                                <div class="d-flex justify-content-between align-items-start">
                                                    <div>
                                                        <div>
                                                            <span class="status-dot {{ $announcement->is_active ? 'status-active' : 'status-inactive' }}"></span>
                                                            {{ Str::limit($announcement->location_name, 30) }}
                                                        </div>
                                                        @if($announcement->note)
                                                            <div class="announcement-location">
                                                                {{ Str::limit($announcement->note, 50) }}
                                                            </div>
                                                        @endif
                                                        <div class="announcement-time">
                                                            {{ $announcement->created_at->diffForHumans() }}
                                                        </div>
                                                    </div>
                                                    <button onclick="toggleAnnouncementStatus({{ $announcement->id }})"
                                                        class="btn btn-sm {{ $announcement->is_active ? 'btn-warning' : 'btn-success' }}">
                                                        {{ $announcement->is_active ? 'Désactiver' : 'Activer' }}
                                                    </button>
                                                </div>
                                            </li>
                                        @empty
                                            <li class="announcement-item text-center text-muted">
                                                Aucune annonce publiée
                                            </li>
                                        @endforelse
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <a class="dropdown-item text-center" href="{{ route('dashboard') }}">
                                                Voir toutes les annonces
                                            </a>
                                        </li>
                                    </ul>
                                </div>

                                <!-- Menu des demandes pour les chauffeurs -->
                                <div class="nav-item dropdown me-3">
                                    <a class="nav-link dropdown-toggle position-relative" href="#" id="requestsDropdown" role="button" data-bs-toggle="dropdown">
                                        <i class="fas fa-bell me-1"></i> Demandes
                                        <span class="badge bg-danger position-absolute top-0 start-100 translate-middle rounded-pill" id="pendingRequestsCount" style="display: none;"></span>
                                    </a>
                                    <ul class="dropdown-menu dropdown-menu-end" id="requestsDropdownMenu" style="width: 300px;">
                                        <li class="px-3 py-2 text-center text-muted" id="noRequestsMessage">
                                            Aucune demande en attente
                                        </li>
                                    </ul>
                                </div>
                            @endif

                            <!-- Menu utilisateur pour tous -->
                            <div class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <img src="{{ Auth::user()->profile_image_url }}"
                                         alt="Profile"
                                         class="rounded-circle me-1"
                                         style="width: 32px; height: 32px; object-fit: cover;">
                                    {{ Auth::user()->name }}
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('dashboard') }}">
                                            <i class="fas fa-tachometer-alt me-2"></i>Tableau de bord
                                        </a>
                                    </li>
                                    @if(auth()->user()->isPassager())
                                        <li>
                                            <a class="dropdown-item" href="#" onclick="showMyRequests()">
                                                <i class="fas fa-list me-2"></i>Mes réservations
                                            </a>
                                        </li>
                                    @endif
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <button type="submit" class="dropdown-item text-danger">
                                                <i class="fas fa-sign-out-alt me-2"></i>Déconnexion
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    @endguest
                </ul>
            </div>
        </div>
    </nav>

    <main class="py-4">
        @yield('content')
    </main>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <script>
        async function toggleAnnouncementStatus(id) {
            try {
                const response = await fetch(`/driver/announcement/${id}/toggle`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });

                if (!response.ok) throw new Error('Erreur lors de la mise à jour');

                // Recharger la page pour mettre à jour l'interface
                window.location.reload();
            } catch (error) {
                alert('Erreur lors de la mise à jour du statut');
            }
        }
    </script>

    @yield('scripts')

    @auth
        @if(auth()->user()->isChauffeur())
            <script>
                // Fonction pour mettre à jour les demandes en attente
                function updatePendingRequests() {
                    fetch('/driver/pending-requests')
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Erreur réseau');
                            }
                            return response.json();
                        })
                        .then(requests => {
                            const menu = document.getElementById('requestsDropdownMenu');
                            const badge = document.getElementById('pendingRequestsCount');
                            const noMessage = document.getElementById('noRequestsMessage');

                            if (requests.length > 0) {
                                badge.textContent = requests.length;
                                badge.style.display = 'block';
                                noMessage.style.display = 'none';

                                menu.innerHTML = requests.map(request => `
                                    <li class="dropdown-item-text border-bottom py-2">
                                        <div class="d-flex align-items-start gap-2">
                                            <img src="${request.passenger.profile_image_url}"
                                                class="rounded-circle"
                                                style="width: 40px; height: 40px; object-fit: cover;">
                                            <div class="flex-grow-1">
                                                <div class="fw-bold">${request.passenger.name}</div>
                                                <div class="small text-muted">
                                                    <i class="fas fa-map-marker-alt me-1"></i>${request.pickup_address}
                                                    <br>
                                                    <i class="fas fa-flag-checkered me-1"></i>${request.destination_address}
                                                </div>
                                                ${request.note ? `
                                                    <div class="small text-muted mt-1">
                                                        <i class="fas fa-info-circle me-1"></i>${request.note}
                                                    </div>
                                                ` : ''}
                                                <div class="small text-muted mt-1">
                                                    <i class="far fa-clock me-1"></i>Prévu pour le ${new Date(request.scheduled_at).toLocaleString()}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-end gap-2 mt-2">
                                            <button onclick="respondToRequest(${request.id}, 'rejected')"
                                                class="btn btn-sm btn-danger">
                                                <i class="fas fa-times me-1"></i>Refuser
                                            </button>
                                            <button onclick="respondToRequest(${request.id}, 'accepted')"
                                                class="btn btn-sm btn-success">
                                                <i class="fas fa-check me-1"></i>Accepter
                                            </button>
                                        </div>
                                    </li>
                                `).join('');
                            } else {
                                badge.style.display = 'none';
                                noMessage.style.display = 'block';
                                menu.innerHTML = `
                                    <li class="dropdown-item-text text-center text-muted py-3">
                                        Aucune demande en attente
                                    </li>
                                `;
                            }
                        })
                        .catch(error => {
                            console.error('Erreur:', error);
                            document.getElementById('requestsDropdownMenu').innerHTML = `
                                <li class="dropdown-item-text text-center text-danger py-3">
                                    Erreur lors du chargement des demandes
                                </li>
                            `;
                        });
                }

                // Fonction pour répondre à une demande
                async function respondToRequest(id, status) {
                    if (!confirm(`Êtes-vous sûr de vouloir ${status === 'accepted' ? 'accepter' : 'refuser'} cette demande ?`)) {
                        return;
                    }

                    try {
                        const response = await fetch(`/driver/ride-request/${id}/respond`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({ status })
                        });

                        if (!response.ok) {
                            const data = await response.json();
                            throw new Error(data.error || 'Erreur lors de la réponse à la demande');
                        }

                        const data = await response.json();
                        alert(data.message);
                        updatePendingRequests(); // Rafraîchir la liste des demandes
                    } catch (error) {
                        alert(error.message);
                    }
                }

                // Mettre à jour les demandes toutes les 30 secondes
                updatePendingRequests();
                setInterval(updatePendingRequests, 30000);
            </script>
        @endif
    @endauth
</body>
</html>

