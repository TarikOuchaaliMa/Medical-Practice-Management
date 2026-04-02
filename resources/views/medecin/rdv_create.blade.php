<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Nouveau Rendez-vous - Espace Médecin</title>
    <link href="{{ asset('css/medecin/rdv_create.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<div class="admin-wrapper">
    
    <aside class="sidebar">
        <div class="sidebar-header"><i class="fas fa-user-md"></i> Espace Docteur</div>
        <div class="doctor-profile">
            <div class="avatar-circle">Dr</div>
            <div class="info">
                <span class="name">{{ Auth::guard('medecin')->user()->nom }}</span>
                <span class="role">{{ Auth::guard('medecin')->user()->specialite }}</span>
            </div>
        </div>
        <nav class="sidebar-nav">
            <a href="{{ route('medecin.dashboard') }}"><i class="fas fa-th-large"></i> Tableau de bord</a>
            <a href="{{ route('medecin.patients') }}"><i class="fas fa-users"></i> Mes Patients</a>
            <a href="{{ route('medecin.planning') }}" class="active"><i class="fas fa-calendar-alt"></i> Planning</a>
            <a href="{{ route('medecin.consultations.list') }}"><i class="fas fa-file-medical-alt"></i> Consultations</a>
        </nav>
        <div class="sidebar-footer">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn-logout"><i class="fas fa-sign-out-alt"></i> Déconnexion</button>
            </form>
        </div>
    </aside>

    <main class="main-content">
        <header class="top-bar">
            <h1>Ajouter un Rendez-vous</h1>
        </header>

        <div class="form-container">
            
            @if ($errors->any())
                <div class="alert-box">
                    <i class="fas fa-exclamation-triangle"></i>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <form action="{{ route('medecin.rdv.store') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label>Sélectionner le Patient *</label>
                    <select name="user_id" class="form-control" required>
                        <option value="">-- Choisir dans la liste --</option>
                        @foreach($patients as $patient)
                            <option value="{{ $patient->id }}" {{ old('user_id') == $patient->id ? 'selected' : '' }}>
                                {{ $patient->nom }} {{ $patient->prenom }} ({{ $patient->telephone }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label>Date du RDV * <small style="color:#64748b; font-weight:normal;">(Min: 12h à l'avance, Max: 1 mois)</small></label>
                    {{-- Limitation HTML native pour le calendrier --}}
                    <input type="date" id="dateInput" name="date" class="form-control" required 
                           min="{{ now()->format('Y-m-d') }}" 
                           max="{{ now()->addMonth()->format('Y-m-d') }}"
                           value="{{ old('date') }}">
                </div>

                <div class="form-group">
                    <label>Heure (Créneaux disponibles) *</label>
                    <div id="slotsGrid" class="slot-grid">
                        <p class="empty-msg"><i class="fas fa-arrow-up"></i> Sélectionnez une date.</p>
                    </div>
                    <input type="hidden" name="time" id="timeInput" required>
                </div>

                <div class="form-group">
                    <label>Motif *</label>
                    <input type="text" name="motif" class="form-control" placeholder="Ex: Urgence, Contrôle..." value="{{ old('motif') }}" required>
                </div>

                <div class="form-actions">
                    <a href="{{ route('medecin.planning') }}" class="btn-cancel">Annuler</a>
                    <button type="submit" class="btn-submit">Confirmer le RDV</button>
                </div>
            </form>
        </div>
    </main>
</div>

<script>
    // 1. Données PHP passées au JS
    const takenSlots = @json($takenSlots); 
    
    // Calcul des limites exactes en JS (Timestamp)
    // On ajoute 12h à l'heure actuelle
    const minTimeLimit = new Date();
    minTimeLimit.setHours(minTimeLimit.getHours() + 12);

    // On ajoute 1 mois à l'heure actuelle
    const maxTimeLimit = new Date();
    maxTimeLimit.setMonth(maxTimeLimit.getMonth() + 1);

    const dateInput = document.getElementById('dateInput');
    const slotsGrid = document.getElementById('slotsGrid');
    const timeInput = document.getElementById('timeInput');

    const workingHours = [
        "09:00", "09:30", "10:00", "10:30", "11:00", "11:30", "12:00", 
        "14:00", "14:30", "15:00", "15:30", "16:00", "16:30", "17:00"
    ];

    dateInput.addEventListener('change', function() {
        const selectedDateStr = this.value;
        slotsGrid.innerHTML = ''; 
        timeInput.value = ''; 

        if (!selectedDateStr) return;

        workingHours.forEach(time => {
            const fullDateTimeStr = `${selectedDateStr} ${time}`; // "2023-11-25 09:30"
            
            // Création d'un objet Date pour ce créneau spécifique
            // Note: On ajoute ":00" pour que le constructeur Date comprenne bien les secondes
            const slotDateObj = new Date(`${selectedDateStr}T${time}:00`);

            const btn = document.createElement('div');
            btn.classList.add('slot-btn');
            btn.innerText = time;

            // --- CONDITIONS DE BLOCAGE ---
            
            // 1. Déjà pris en BDD ?
            const isTaken = takenSlots.includes(fullDateTimeStr);
            
            // 2. Trop tôt ? (< 12h)
            const isTooSoon = slotDateObj < minTimeLimit;

            // 3. Trop tard ? (> 1 mois)
            const isTooLate = slotDateObj > maxTimeLimit;

            if (isTaken || isTooSoon || isTooLate) {
                btn.classList.add('taken');
                
                // Message d'infobulle personnalisé
                if(isTaken) btn.title = "Déjà réservé";
                else if(isTooSoon) btn.title = "Délai min: 12h";
                else if(isTooLate) btn.title = "Délai max: 1 mois";
                
            } else {
                // Créneau Libre
                btn.onclick = function() {
                    document.querySelectorAll('.slot-btn').forEach(b => b.classList.remove('selected'));
                    btn.classList.add('selected');
                    timeInput.value = time;
                };
            }

            slotsGrid.appendChild(btn);
        });

        if(slotsGrid.children.length === 0) {
            slotsGrid.innerHTML = '<p class="empty-msg">Aucun créneau disponible.</p>';
        }
    });
</script>

</body>
</html>