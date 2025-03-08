@extends('layouts.app')

@section('styles')
<style>
    .chat-container {
        height: calc(100vh - 200px);
        display: flex;
        flex-direction: column;
        background-color: #f8fafc;
        border-radius: 0.5rem;
        overflow: hidden;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }

    .chat-header {
        padding: 1rem;
        background-color: #4f46e5;
        color: white;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .chat-header h2 {
        margin: 0;
        font-size: 1.25rem;
    }

    .chat-header .recipient-info {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .chat-header .recipient-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid white;
    }

    .chat-messages {
        flex: 1;
        overflow-y: auto;
        padding: 1rem;
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .message {
        max-width: 80%;
        padding: 0.75rem 1rem;
        border-radius: 0.5rem;
        position: relative;
    }

    .message-time {
        font-size: 0.75rem;
        opacity: 0.7;
        margin-top: 0.25rem;
        text-align: right;
    }

    .message-sender {
        align-self: flex-end;
        background-color: #4f46e5;
        color: white;
        border-bottom-right-radius: 0;
    }

    .message-recipient {
        align-self: flex-start;
        background-color: #e5e7eb;
        color: #1f2937;
        border-bottom-left-radius: 0;
    }

    .chat-input {
        padding: 1rem;
        border-top: 1px solid #e5e7eb;
        background-color: white;
    }

    .chat-input form {
        display: flex;
        gap: 0.5rem;
    }

    .chat-input textarea {
        flex: 1;
        padding: 0.75rem;
        border: 1px solid #e5e7eb;
        border-radius: 0.375rem;
        resize: none;
        height: 60px;
    }

    .chat-input button {
        align-self: flex-end;
        padding: 0.75rem 1.5rem;
        background-color: #4f46e5;
        color: white;
        border: none;
        border-radius: 0.375rem;
        cursor: pointer;
        transition: background-color 0.2s;
    }

    .chat-input button:hover {
        background-color: #4338ca;
    }

    .ride-info {
        margin-bottom: 1rem;
        padding: 1rem;
        background-color: white;
        border-radius: 0.5rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .ride-info h3 {
        margin-top: 0;
        font-size: 1.125rem;
        color: #1f2937;
    }

    .ride-details {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
        margin-top: 0.5rem;
    }

    .ride-detail {
        display: flex;
        align-items: flex-start;
        gap: 0.5rem;
    }

    .ride-detail i {
        color: #4f46e5;
        margin-top: 0.25rem;
    }

    .ride-detail-content {
        flex: 1;
    }

    .ride-detail-label {
        font-size: 0.75rem;
        color: #6b7280;
    }

    .ride-detail-value {
        color: #1f2937;
    }

    .back-button {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
        background-color: #f3f4f6;
        color: #4b5563;
        border: none;
        border-radius: 0.375rem;
        cursor: pointer;
        transition: background-color 0.2s;
        text-decoration: none;
        margin-bottom: 1rem;
    }

    .back-button:hover {
        background-color: #e5e7eb;
    }

    .empty-state {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 100%;
        color: #6b7280;
    }

    .empty-state i {
        font-size: 3rem;
        margin-bottom: 1rem;
    }
</style>
@endsection

@section('content')
<div class="container py-4">
    <a href="{{ url()->previous() }}" class="back-button">
        <i class="fas fa-arrow-left"></i>
        <span>Retour</span>
    </a>

    <div class="row">
        <div class="col-md-4">
            <div class="ride-info">
                <h3>Détails de la course</h3>
                <div class="ride-details">
                    <div class="ride-detail">
                        <i class="fas fa-calendar"></i>
                        <div class="ride-detail-content">
                            <div class="ride-detail-label">Date et heure</div>
                            <div class="ride-detail-value">{{ date('d/m/Y H:i', strtotime($ride->scheduled_at)) }}</div>
                        </div>
                    </div>
                    <div class="ride-detail">
                        <i class="fas fa-map-marker-alt"></i>
                        <div class="ride-detail-content">
                            <div class="ride-detail-label">Départ</div>
                            <div class="ride-detail-value">{{ $ride->pickup_address }}</div>
                        </div>
                    </div>
                    <div class="ride-detail">
                        <i class="fas fa-flag-checkered"></i>
                        <div class="ride-detail-content">
                            <div class="ride-detail-label">Destination</div>
                            <div class="ride-detail-value">{{ $ride->destination_address }}</div>
                        </div>
                    </div>
                    <div class="ride-detail">
                        <i class="fas fa-info-circle"></i>
                        <div class="ride-detail-content">
                            <div class="ride-detail-label">Statut</div>
                            <div class="ride-detail-value">
                                @if($ride->status == 'pending')
                                    <span class="badge bg-warning text-dark">En attente</span>
                                @elseif($ride->status == 'accepted')
                                    <span class="badge bg-success">Acceptée</span>
                                @elseif($ride->status == 'rejected')
                                    <span class="badge bg-danger">Refusée</span>
                                @elseif($ride->status == 'cancelled')
                                    <span class="badge bg-secondary">Annulée</span>
                                @elseif($ride->status == 'completed')
                                    <span class="badge bg-primary">Terminée</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    @if($ride->note)
                    <div class="ride-detail">
                        <i class="fas fa-sticky-note"></i>
                        <div class="ride-detail-content">
                            <div class="ride-detail-label">Note</div>
                            <div class="ride-detail-value">{{ $ride->note }}</div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="chat-container">
                <div class="chat-header">
                    <div class="recipient-info">
                        <img src="{{ $recipient->profile_image_url ?? '/images/default-avatar.png' }}" alt="{{ $recipient->name }}" class="recipient-avatar">
                        <div>
                            <h2>{{ $recipient->name }}</h2>
                            <small>{{ $userType == 'passenger' ? 'Chauffeur' : 'Passager' }}</small>
                        </div>
                    </div>
                    <div>
                        <i class="fas fa-phone me-2"></i>{{ $recipient->phone }}
                    </div>
                </div>

                <div class="chat-messages" id="chat-messages">
                    @if(count($messages) > 0)
                        @foreach($messages as $message)
                            <div class="message {{ $message->sender_id == Auth::id() ? 'message-sender' : 'message-recipient' }}">
                                <div class="message-content">{{ $message->message }}</div>
                                <div class="message-time">{{ date('H:i', strtotime($message->created_at)) }}</div>
                            </div>
                        @endforeach
                    @else
                        <div class="empty-state">
                            <i class="far fa-comments"></i>
                            <p>Aucun message. Commencez la conversation!</p>
                        </div>
                    @endif
                </div>

                <div class="chat-input">
                    <form id="message-form">
                        <textarea id="message-input" placeholder="Tapez votre message ici..." required></textarea>
                        <button type="submit">
                            <i class="fas fa-paper-plane me-2"></i>Envoyer
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const messagesContainer = document.getElementById('chat-messages');
    const messageForm = document.getElementById('message-form');
    const messageInput = document.getElementById('message-input');

    // Scroll to bottom of messages
    function scrollToBottom() {
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }

    // Scroll to bottom on page load
    scrollToBottom();

    // Handle form submission
    messageForm.addEventListener('submit', async function(e) {
        e.preventDefault();

        const message = messageInput.value.trim();
        if (!message) return;

        try {
            const response = await fetch('/api/messages', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    ride_id: {{ $ride->id }},
                    recipient_id: {{ $recipient->id }},
                    recipient_type: '{{ $userType == "passenger" ? "driver" : "passenger" }}',
                    message: message
                })
            });

            const data = await response.json();

            if (!data.success) {
                throw new Error(data.error || 'Erreur lors de l\'envoi du message');
            }

            // Clear input
            messageInput.value = '';
            messageInput.focus();

        } catch (error) {
            console.error('Erreur:', error);
            alert('Erreur lors de l\'envoi du message. Veuillez réessayer.');
        }
    });

    // Listen for new messages
    window.Echo.channel('ride-chat')
        .listen('.new-message', (data) => {
            // Only show messages for the current ride
            if (data.message.ride_id == {{ $ride->id }}) {
                // Create message element
                const messageElement = document.createElement('div');
                messageElement.className = `message ${data.message.sender_id == {{ Auth::id() }} ? 'message-sender' : 'message-recipient'}`;

                // Create message content
                messageElement.innerHTML = `
                    <div class="message-content">${data.message.message}</div>
                    <div class="message-time">${new Date(data.message.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}</div>
                `;

                // Add to chat container
                messagesContainer.appendChild(messageElement);

                // Scroll to bottom
                scrollToBottom();
            }
        });

    // Allow sending message with Enter key (but Shift+Enter for new line)
    messageInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            messageForm.dispatchEvent(new Event('submit'));
        }
    });
});
</script>
@endsection

