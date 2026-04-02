<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vérification Email - Cabinet Dr. El Halimi</title>
    <link href="{{ asset('css/auth/verify.css') }}" rel="stylesheet">
</head>
<body class="auth-body">

    <header class="main-header">
        <div class="logo"><span>🩺</span><span>Cabinet Dr. El Halimi</span></div>
    </header>

    <div class="auth-container">
        <div class="auth-card" style="text-align: center;">
            <div style="font-size: 3rem; margin-bottom: 20px;">✉️</div>
            
            <h2 class="auth-title">Vérifiez vos emails</h2>
            
            <p style="color: #4b5563; margin-bottom: 20px; line-height: 1.6;">
                Merci pour votre inscription !<br>
                Avant de commencer, veuillez cliquer sur le lien que nous venons de vous envoyer par email.
            </p>

            {{-- Message de succès si on renvoie le lien --}}
            @if (session('status') == 'verification-link-sent')
                <div style="background: #dcfce7; color: #166534; padding: 10px; border-radius: 8px; margin-bottom: 20px;">
                    Un nouveau lien de vérification a été envoyé à votre adresse email.
                </div>
            @endif

            <div style="display: flex; gap: 10px; justify-content: center; flex-wrap: wrap;">
                
                {{-- Bouton Renvoyer --}}
                <form method="POST" action="{{ route('verification.send') }}">
                    @csrf
                    <button type="submit" class="btn-primary">
                        Renvoyer l'email
                    </button>
                </form>

                {{-- Bouton Déconnexion (au cas où il s'est trompé de mail) --}}
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn-secondary" style="background:white; cursor:pointer;">
                        Déconnexion
                    </button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>