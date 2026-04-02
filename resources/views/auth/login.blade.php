<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion - Cabinet Médical</title>
    <link href="{{ asset('css/auth/login.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="auth-body">

    {{-- LIEN RETOUR --}}
    <a href="{{ route('home') }}" class="back-link">
        <i class="fas fa-arrow-left"></i> Retour à l'accueil
    </a>

    <div class="auth-wrapper">
        
        {{-- HEADER LOGO --}}
        <div class="auth-header">
            <div class="logo-circle">
                <i class="fas fa-stethoscope"></i>
            </div>
            <h2>Cabinet Médical</h2>
            <p>Système de gestion médicale</p>
        </div>

        {{-- CARTE DE CONNEXION --}}
        <div class="auth-card">
            
            <h3 class="card-title">Connexion</h3>
            <p class="card-subtitle">Accédez à votre espace</p>

            {{-- SÉLECTEUR D'ONGLETS (TABS) --}}
            <div class="tab-container">
                <button class="tab-btn active" onclick="switchTab('patient')">
                    <i class="far fa-user"></i> Patient
                </button>
                <button class="tab-btn" onclick="switchTab('personnel')">
                    <i class="fas fa-lock"></i> Personnel
                </button>
            </div>

            {{-- FORMULAIRE PATIENT --}}
            <div id="patient-form" class="form-section active">
                <form action="{{ route('login') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label>Email Patient</label>
                        <input type="email" name="email" class="form-input" placeholder="ex: patient@email.com" required value="{{ old('email') }}">
                        @error('email') <span class="error-text">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label>Mot de passe</label>
                        <input type="password" name="password" class="form-input" placeholder="••••••••" required>
                    </div>
                    
                    <button type="submit" class="btn-submit">
                        Se connecter
                    </button>
                </form>
                
                <div class="demo-box">
                    <div style="margin-top: 5px;">
                        <a href="{{ route('register') }}">Créer un compte patient</a>
                    </div>
                </div>
            </div>

            <div id="personnel-form" class="form-section">
                <form action="{{ route('login.medecin') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label>Email professionnel</label>
                        <input type="email" name="email" class="form-input" placeholder="docteur@cabinet.fr" required>
                    </div>
                    <div class="form-group">
                        <label>Mot de passe</label>
                        <input type="password" name="password" class="form-input" placeholder="••••••••" required>
                    </div>

                    <button type="submit" class="btn-submit">
                        Se connecter
                    </button>
                </form>
            </div>

        </div>
    </div>

    <script>
        function switchTab(type) {
            const patientForm = document.getElementById('patient-form');
            const personnelForm = document.getElementById('personnel-form');
            const btns = document.querySelectorAll('.tab-btn');

            if (type === 'patient') {
                patientForm.style.display = 'block';
                personnelForm.style.display = 'none';
                btns[0].classList.add('active');
                btns[1].classList.remove('active');
            } else {
                patientForm.style.display = 'none';
                personnelForm.style.display = 'block';
                btns[0].classList.remove('active');
                btns[1].classList.add('active');
            }
        }
    </script>

</body>
</html>