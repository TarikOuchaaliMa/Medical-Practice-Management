<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Espace Médecin - Planning</title>
    <link href="{{ asset('css/medecin/planning.css') }}" rel="stylesheet">
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
            <a href="{{ route('medecin.dashboard') }}">
                <i class="fas fa-th-large"></i> Tableau de bord
            </a>
            <a href="{{ route('medecin.patients') }}">
                <i class="fas fa-users"></i> Mes Patients
            </a>
            <a href="{{ route('medecin.planning') }}" class="active">
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
            <h1>Mon Planning de Rendez-vous</h1>
        </header>

        {{-- PLANNING LIST SECTION --}}
        <div class="section-container">
            <div class="section-header">
                <h3>Total des Rendez-vous ({{ $rendezVous->total() }} enregistrés)</h3>
                <a href="{{ route('medecin.rdv.create') }}"><button class="btn-secondary"><i class="fas fa-plus"></i> Créer un nouveau RDV</button></a>
            </div>

            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Date et Heure</th>
                        <th>Patient</th>
                        <th>Motif</th>
                        <th>Statut</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rendezVous as $rdv)
                        <tr>
                            <td>
                                <div class="date-time-cell">
                                    <span class="date-main">{{ \Carbon\Carbon::parse($rdv->date_heure)->translatedFormat('d F Y') }}</span>
                                    <span class="time-sub">{{ \Carbon\Carbon::parse($rdv->date_heure)->format('H:i') }}</span>
                                </div>
                            </td>
                            <td>
                                <div class="patient-info">
                                    <strong>{{ $rdv->patient->nom }} {{ $rdv->patient->prenom }}</strong>
                                    <small>{{ $rdv->patient->code_dossier }}</small>
                                </div>
                            </td>
                            <td>{{ $rdv->motif }}</td>
                            <td>
                                <span class="badge {{ $rdv->statut }}">
                                    {{ $rdv->statut == 'en_attente' ? 'À venir' : ucfirst($rdv->statut) }}
                                </span>
                            </td>
                            <td>
                                @if($rdv->statut == 'confirme' || $rdv->statut == 'en_attente')
                                    <a href="{{ route('medecin.consultation.create', $rdv->id) }}" class="btn-action start-consultation">
                                        <i class="fas fa-stethoscope"></i> Démarrer
                                    </a>
                                @elseif($rdv->statut == 'termine')
                                    <a href="{{ route('medecin.consultation.show', $rdv->consultation->id) }}" class="btn-action view-dossier" title="Voir la consultation terminée">
                                        <i class="fas fa-eye"></i> Voir
                                    </a>
                                @else
                                    <span class="text-muted">--</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="empty-row">
                                <i class="fas fa-calendar-times"></i> Aucun rendez-vous trouvé.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            {{-- PAGINATION LINKS --}}
            <div class="pagination-links">
                {{ $rendezVous->links() }}
            </div>
        </div>

    </main>
</div>

</body>
</html>