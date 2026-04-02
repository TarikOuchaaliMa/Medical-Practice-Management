<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mes Rendez-vous</title>
    <link href="{{ asset('css/patient/rendezvous.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<div class="page-wrapper">
    
    <aside class="sidebar">
        <div class="sidebar-brand"><i class="fas fa-heartbeat"></i> Cabinet Dr. El Halimi</div>
        <div class="user-box">
            <div class="user-avatar">{{ substr($user->prenom, 0, 1) }}{{ substr($user->nom, 0, 1) }}</div>
            <div class="user-details">
                <strong>{{ $user->prenom }} {{ $user->nom }}</strong>
                <span>Patient</span>
            </div>
        </div>
        <nav class="nav-links">
            <a href="{{ route('patient.dashboard') }}"><i class="fas fa-home"></i> Tableau de bord</a>
            <a href="{{ route('patient.rendezvous') }}" class="active"><i class="fas fa-calendar"></i> Rendez-vous</a>
            <a href="{{ route('patient.dossier') }}"><i class="fas fa-file-medical"></i> Dossier Médical</a>
            <a href="{{ route('patient.ordonnances') }}"><i class="fas fa-pills"></i> Ordonnances</a>
        </nav>
        <div class="sidebar-footer">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn-logout"><i class="fas fa-sign-out-alt"></i> Déconnexion</button>
            </form>
        </div>
    </aside>

    <main class="main-content">
        <header class="page-header">
            <div>
                <h1>Mes Rendez-vous 📅</h1>
                <p>Planifiez vos consultations (Délai min: 12h).</p>
            </div>
        </header>

        @if(session('success'))
            <div class="alert-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
        @endif

        @if($errors->any())
            <div class="alert-error">
                <ul>@foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach</ul>
            </div>
        @endif

        <div class="split-layout">
            
            <section class="left-panel">
                <h3 class="section-title">À venir</h3>
                <div class="rdv-list">
                    @forelse($upcoming as $rdv)
                        <div class="rdv-card {{ $rdv->statut }}">
                            <div class="rdv-date-box">
                                <span class="d">{{ \Carbon\Carbon::parse($rdv->date_heure)->format('d') }}</span>
                                <span class="m">{{ \Carbon\Carbon::parse($rdv->date_heure)->format('M') }}</span>
                                <span class="time">{{ \Carbon\Carbon::parse($rdv->date_heure)->format('H:i') }}</span>
                            </div>
                            <div class="rdv-info">
                                <h4>{{ $rdv->motif }}</h4>
                                <span class="doc">Dr. {{ $rdv->medecin->nom }}</span>
                                <div class="badges">
                                    <span class="badge {{ $rdv->statut }}">{{ ucfirst($rdv->statut) }}</span>
                                </div>
                            </div>
                            <div class="rdv-actions">
                                @if($rdv->statut != 'annule')
                                    <form action="{{ route('rendezvous.cancel', $rdv->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn-cancel" onclick="return confirm('Annuler ce RDV ?')"><i class="fas fa-times"></i></button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="empty-box"><p>Aucun rendez-vous programmé.</p></div>
                    @endforelse
                </div>
            </section>

            <section class="right-panel">
                <div class="booking-card">
                    <div class="booking-header">
                        <div class="icon-circle"><i class="fas fa-plus"></i></div>
                        <h3>Nouveau Rendez-vous</h3>
                    </div>
                    
                    <form action="{{ route('rendezvous.store') }}" method="POST" class="booking-form" id="bookingForm">
                        @csrf
                        
                        <div class="form-group">
                            <label>Médecin</label>
                            <select name="medecin_id" class="form-input" required>
                                @foreach($medecins as $medecin)
                                    <option value="{{ $medecin->id }}">Dr. {{ $medecin->nom }} ({{ $medecin->specialite }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Date souhaitée</label>
                            <input type="date" name="date" id="dateInput" class="form-input" required 
                                   min="{{ now()->addDay()->format('Y-m-d') }}" 
                                   max="{{ now()->addMonth()->format('Y-m-d') }}">
                        </div>

                        <div class="form-group slot-container">
                            <label>Heure (Créneaux disponibles)</label>
                            <div id="slotsGrid" class="slot-grid">
                                <p style="font-size:0.9rem; color:#6b7280;">Sélectionnez une date d'abord.</p>
                            </div>
                            <input type="hidden" name="time" id="timeInput" required>
                        </div>

                        <div class="form-group">
                            <label>Motif</label>
                            <input type="text" name="motif" placeholder="Ex: Fièvre..." class="form-input" required>
                        </div>

                        <button type="submit" class="btn-submit">Confirmer</button>
                    </form>
                </div>
            </section>

        </div>
    </main>
</div>

<script>
    
    const takenSlots = @json($takenSlots);    
    const dateInput = document.getElementById('dateInput');
    const slotsGrid = document.getElementById('slotsGrid');
    const timeInput = document.getElementById('timeInput');

    const workingHours = ["09:00", "09:30", "10:00", "10:30", "11:00", "11:30", "12:00", "14:00", "14:30", "15:00", "15:30", "16:00", "16:30", "17:00"];

    dateInput.addEventListener('change', function() {
        const selectedDate = this.value;
        slotsGrid.innerHTML = ''; 
        timeInput.value = ''; 

        if (!selectedDate) return;

        workingHours.forEach(time => {
            const fullDateTime = `${selectedDate} ${time}`;
            const btn = document.createElement('div');
            btn.classList.add('slot-btn');
            btn.innerText = time;
            if (takenSlots.includes(fullDateTime)) {
                btn.classList.add('taken'); 
                btn.title = "Déjà réservé";
            } else {
                btn.onclick = function() {
                    document.querySelectorAll('.slot-btn').forEach(b => b.classList.remove('selected'));
                    btn.classList.add('selected');
                    timeInput.value = time;
                };
            }

            slotsGrid.appendChild(btn);
        });
    });
</script>

</body>
</html>