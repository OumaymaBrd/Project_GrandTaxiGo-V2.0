<!DOCTYPE html>
<html>
<head>
    <title>Envoi de SMS</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            padding: 20px;
        }
        .card {
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            margin-top: 20px;
        }
        .card-header {
            background-color: #0d6efd;
            color: white;
            font-weight: bold;
        }
        .btn-primary {
            background-color: #0d6efd;
            border-color: #0d6efd;
        }
        .alert {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <h1 class="text-center mb-4">Envoi de SMS</h1>

                @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                @endif

                <div class="card">
                    <div class="card-header">Envoyer un SMS</div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('sms.send') }}">
                            @csrf
                            <div class="mb-3">
                                <label for="phone" class="form-label">Numéro de téléphone</label>
                                <input type="text" class="form-control @error('phone') is-invalid @enderror"
                                       id="phone" name="phone" value="{{ old('phone') }}"
                                       placeholder="0612345678 ou +212612345678" required>
                                <div class="form-text">Format marocain: 0612345678 ou +212612345678</div>
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="message" class="form-label">Message</label>
                                <textarea class="form-control @error('message') is-invalid @enderror"
                                          id="message" name="message" rows="3" required>{{ old('message', 'Ceci est un message de test.') }}</textarea>
                                <div class="d-flex justify-content-between mt-1">
                                    <small class="text-muted">Caractères: <span id="char-count">0</span>/160</small>
                                    <small class="text-muted">SMS: <span id="sms-count">1</span></small>
                                </div>
                                @error('message')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Envoyer SMS</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const messageField = document.getElementById('message');
            const charCount = document.getElementById('char-count');
            const smsCount = document.getElementById('sms-count');

            function updateCounter() {
                const text = messageField.value;
                const length = text.length;
                charCount.textContent = length;

                // Calculate SMS count (160 chars for first SMS, 153 for subsequent)
                let count = 1;
                if (length > 160) {
                    count = Math.ceil((length - 160) / 153) + 1;
                }
                smsCount.textContent = count;

                // Change color if over limit
                if (length > 160) {
                    charCount.style.color = 'orange';
                } else {
                    charCount.style.color = '';
                }
            }

            messageField.addEventListener('input', updateCounter);
            updateCounter(); 
        });
    </script>
</body>
</html>

