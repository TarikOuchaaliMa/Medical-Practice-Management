<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Inscription - Cabinet Dr. El Halimi</title>
    <link href="{{ asset('css/auth/register.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="auth-body">
        <a href="{{ route('home') }}" class="back-link">
        <i class="fas fa-arrow-left"></i> Retour à l'accueil
    </a>
    <div class="auth-container">
        <div class="auth-card">
            <h2 class="auth-title">Nouveau Dossier Patient</h2>

            <form action="{{ route('register') }}" method="POST">
                @csrf 
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Prénom *</label>
                        <input type="text" name="prenom" class="form-control" value="{{ old('prenom') }}" required>
                        @error('prenom') <div class="error-msg">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-group">
                        <label>Nom *</label>
                        <input type="text" name="nom" class="form-control" value="{{ old('nom') }}" required>
                        @error('nom') <div class="error-msg">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Date de naissance (+18) *</label>
                        <input type="date" name="date_naissance" class="form-control" value="{{ old('date_naissance') }}" required>
                        @error('date_naissance') <div class="error-msg">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-group">
                        <label>Téléphone (10 chiffres) *</label>
                        <input type="text" name="telephone" class="form-control" placeholder="06..." value="{{ old('telephone') }}" required>
                        @error('telephone') <div class="error-msg">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="form-group">
                    <label>Adresse (Cliquez sur la carte) *</label>
                    <input type="text" id="adresse" name="adresse" class="form-control" readonly required value="{{ old('adresse') }}">
                    <div id="map" style="height: 250px; margin-top: 10px; border-radius: 8px;"></div>
                    @error('adresse') <div class="error-msg">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label>Groupe Sanguin</label>
                    <select name="groupe_sanguin" class="form-control">
                        <option value="">-- Sélectionner --</option>
                        <option value="A+">A+</option><option value="A-">A-</option>
                        <option value="B+">B+</option><option value="B-">B-</option>
                        <option value="AB+">AB+</option><option value="AB-">AB-</option>
                        <option value="O+">O+</option><option value="O-">O-</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Allergies Connues</label>
                    <textarea name="allergies_connues" class="form-control" rows="2">{{ old('allergies_connues') }}</textarea>
                </div>

                <div class="form-group">
                    <label>Email *</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                    @error('email') <div class="error-msg">{{ $message }}</div> @enderror
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Mot de passe *</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Confirmer *</label>
                        <input type="password" name="password_confirmation" class="form-control" required>
                    </div>
                </div>
                @error('password') <div class="error-msg">{{ $message }}</div> @enderror

                <button type="submit" class="btn-primary btn-block">S'inscrire</button>
            </form>
        </div>
    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        var map = L.map('map').setView([35.17401913969285, -2.9351293601387582], 13);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
        var marker;
        map.on('click', function(e) {
            if (marker) marker.setLatLng(e.latlng);
            else marker = L.marker(e.latlng).addTo(map);
            fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${e.latlng.lat}&lon=${e.latlng.lng}`)
                .then(res => res.json())
                .then(data => document.getElementById('adresse').value = data.display_name || (e.latlng.lat + ", " + e.latlng.lng));
        });
    </script>
</body>
</html>