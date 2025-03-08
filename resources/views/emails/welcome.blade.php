<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Bienvenue sur notre application</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #4a6cf7;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        .content {
            padding: 20px;
            border: 1px solid #ddd;
            border-top: none;
            border-radius: 0 0 5px 5px;
        }
        .button {
            display: inline-block;
            background-color: #4a6cf7;
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 5px;
            margin-top: 20px;
        }
        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 12px;
            color: #777;
        }
        .notification-box {
            background-color: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 5px;
            padding: 15px;
            margin: 20px 0;
            position: relative;
        }
        .notification-icon {
            position: absolute;
            top: -10px;
            right: -10px;
            background-color: #ff5722;
            color: white;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }
        .qr-code-container {
            text-align: center;
            margin: 20px 0;
        }
        .qr-code {
            border: 1px solid #ddd;
            padding: 10px;
            display: inline-block;
            background-color: white;
        }
        .code-text {
            font-family: monospace;
            font-size: 18px;
            font-weight: bold;
            letter-spacing: 2px;
            margin-top: 10px;
            color: #333;
        }
        .connection-box {
            background-color: #e8f4fd;
            border: 1px solid #b8daff;
            border-radius: 5px;
            padding: 15px;
            margin: 20px 0;
        }
        .connection-title {
            color: #004085;
            font-weight: bold;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Bienvenue {{ $user->name }}!</h1>
    </div>

    <div class="content">
        <p>Nous sommes ravis de vous accueillir sur notre application.</p>

        <p>Votre compte a été créé avec succès avec les informations suivantes:</p>
        <ul>
            <li><strong>Email:</strong> {{ $user->email }}</li>
            <li><strong>Rôle:</strong> {{ ucfirst($user->role) }}</li>
        </ul>

        <!-- Notification avec icône NB -->
        <div class="notification-box">
            <div class="notification-icon">NB</div>
            <p><strong>Important:</strong> Après avoir effectué une réservation, vous recevrez une notification pour vous connecter en temps réel avec votre {{ $user->role == 'chauffeur' ? 'passager' : 'chauffeur' }}.</p>
        </div>

        <!-- Code QR avec nombre aléatoire -->
        <div class="qr-code-container">
            <h3>Votre code de connexion personnel</h3>
            <p>Utilisez ce code QR pour vous connecter rapidement avec votre {{ $user->role == 'chauffeur' ? 'passager' : 'chauffeur' }}:</p>

            <div class="qr-code">
                <img src="{{ $qrCodeUrl }}" alt="Code QR de connexion" width="150" height="150">
            </div>

            <div class="code-text">
                {{ $connectionCode }}
            </div>
            <p>Ce code est unique et personnel. Ne le partagez pas avec d'autres personnes.</p>
        </div>

        <!-- Instructions de connexion en temps réel -->
        <div class="connection-box">
            <div class="connection-title">Comment se connecter en temps réel:</div>
            <ol>
                <li>Ouvrez l'application sur votre téléphone</li>
                <li>Allez dans la section "Mes réservations"</li>
                <li>Sélectionnez la réservation active</li>
                <li>Appuyez sur le bouton "Connexion en temps réel"</li>
                <li>Scannez le code QR ou entrez le code à 6 chiffres</li>
                <li>Vous serez immédiatement connecté avec votre {{ $user->role == 'chauffeur' ? 'passager' : 'chauffeur' }}</li>
            </ol>
        </div>

        <p>Vous pouvez maintenant vous connecter et commencer à utiliser notre application.</p>

        <a href="{{ route('dashboard') }}" class="button">Accéder à votre tableau de bord</a>

        <p>Si vous avez des questions ou besoin d'aide, n'hésitez pas à nous contacter.</p>

        <p>Cordialement,<br>L'équipe de l'application</p>
    </div>

    <div class="footer">
        <p>Cet email a été envoyé automatiquement. Merci de ne pas y répondre.</p>
        <p>&copy; {{ date('Y') }} Votre Application. Tous droits réservés.</p>
    </div>
</body>
</html>

