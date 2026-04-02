<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Espace Médecin - Dashboard</title>
    <link href="{{ asset('css/medecin/dashboard.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<div class="admin-wrapper">
    
    {{-- SIDEBAR --}}
    <aside class="sidebar">
        <div class="sidebar-header">
            <i class="fas fa-user-md"></i> Espace Docteur
        </div>
        
        <div class="doctor-profile">
            <div class="avatar-circle">Dr</div>
            <div class="info">
                <span class="name">{{ $medecin->prenom }} {{ $medecin->nom }}</span>
                <span class="role">{{ $medecin->specialite }}</span>
            </div>
        </div>

        <nav class="sidebar-nav">
            <a href="{{ route('medecin.dashboard') }}" class="active">
                <i class="fas fa-th-large"></i> Tableau de bord
            </a>
            <a href="{{ route('medecin.patients') }}">
                <i class="fas fa-users"></i> Mes Patients
            </a>
            <a href="{{ route('medecin.planning') }}">
                <i class="fas fa-calendar-alt"></i> Planning
            </a>
            <a href="{{ route('medecin.consultations.list') }}">
                <i class="fas fa-file-medical-alt"></i> Consultations
            </a>
        </nav>

        <div class="sidebar-footer">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn-logout">
                    <i class="fas fa-sign-out-alt"></i> Déconnexion
                </button>
            </form>
        </div>
    </aside>

    {{-- MAIN CONTENT --}}
    <main class="main-content">
        
        <header class="top-bar">
            <h1>Bonjour, Dr. {{ $medecin->nom }} 👋</h1>
            <div class="date-today">{{ \Carbon\Carbon::now()->translatedFormat('l d F Y') }}</div>
        </header>

        {{-- CARTES STATISTIQUES --}}
        <div class="stats-grid">
            
            <div class="stat-card blue">
                <div class="icon"><i class="fas fa-calendar-check"></i></div>
                <div class="details">
                    <span class="number">{{ $rdvTodayCount }}</span>
                    <span class="label">RDV Aujourd'hui</span>
                </div>
            </div>

            <div class="stat-card orange">
                <div class="icon"><i class="fas fa-clock"></i></div>
                <div class="details">
                    <span class="number">{{ $rdvPendingCount }}</span>
                    <span class="label">En attente</span>
                </div>
            </div>

            <div class="stat-card green">
                <div class="icon"><i class="fas fa-user-injured"></i></div>
                <div class="details">
                    <span class="number">{{ $totalPatients }}</span>
                    <span class="label">Patients Total</span>
                </div>
            </div>

        </div>

        {{-- TABLEAU DU JOUR --}}
        <div class="section-container">
            <div class="section-header">
                <h3>Planning du jour</h3>
                <a href="{{ route('medecin.rdv.create') }}"><button class="btn-secondary"><i class="fas fa-plus"></i> Ajouter un RDV</button></a>
            </div>

            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Heure</th>
                        <th>Patient</th>
                        <th>Motif</th>
                        <th>Statut</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($todaysRdvs as $rdv)
                        <tr>
                            <td class="time-cell">
                                <i class="far fa-clock"></i> {{ \Carbon\Carbon::parse($rdv->date_heure)->format('H:i') }}
                            </td>
                            <td>
                                <div class="patient-info">
                                    <strong>{{ $rdv->patient->nom }} {{ $rdv->patient->prenom }}</strong>
                                    <small>{{ $rdv->patient->telephone }}</small>
                                </div>
                            </td>
                            <td>{{ $rdv->motif }}</td>
                            <td>
                                <span class="badge {{ $rdv->statut }}">
                                    {{ $rdv->statut == 'en_attente' ? 'À venir' : ucfirst($rdv->statut) }}
                                </span>
                            </td>
                            <td>
                                @if($rdv->statut != 'termine' && $rdv->statut != 'annule')
                                    <a href="{{ route('medecin.consultation.create', $rdv->id) }}" class="btn-action">
                                        <i class="fas fa-stethoscope"></i> Démarrer
                                    </a>
                                @else
                                    <span class="text-muted">--</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="empty-row">
                                <i class="fas fa-coffee"></i> Aucun rendez-vous prévu pour aujourd'hui.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </main>
</div>
</body>
</html>