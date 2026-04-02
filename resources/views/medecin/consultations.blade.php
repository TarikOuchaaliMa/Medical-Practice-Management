<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Espace Médecin - Historique Consultations</title>
    <link href="{{ asset('css/medecin/consultations.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<div class="admin-wrapper">
    
    {{-- SIDEBAR --}}
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
            <a href="{{ route('medecin.dashboard') }}">
                <i class="fas fa-th-large"></i> Tableau de bord
            </a>
            <a href="{{ route('medecin.patients') }}">
                <i class="fas fa-users"></i> Mes Patients
            </a>
            <a href="{{ route('medecin.planning') }}">
                <i class="fas fa-calendar-alt"></i> Planning
            </a>
            {{-- Lien Actif --}}
            <a href="{{ route('medecin.consultations.list') }}" class="active">
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
            <h1>Historique des Consultations</h1>
        </header>

        <div class="section-container">
            <div class="section-header">
                <h3>Total : {{ $consultations->total() }} consultation(s)</h3>
            </div>

            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Patient</th>
                        <th>Titre / Motif</th>
                        <th>Diagnostic</th>
                        <th>Traitement</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($consultations as $consultation)
                        <tr>
                            <td>
                                {{ $consultation->created_at->format('d/m/Y') }}
                                <br>
                                <small style="color:#9ca3af;">{{ $consultation->created_at->format('H:i') }}</small>
                            </td>
                            <td>
                                @if($consultation->rendezVous && $consultation->rendezVous->patient)
                                    <div class="patient-info">
                                        <strong>{{ $consultation->rendezVous->patient->nom }} {{ $consultation->rendezVous->patient->prenom }}</strong>
                                        <small>{{ $consultation->rendezVous->patient->telephone }}</small>
                                    </div>
                                @else
                                    <span class="text-muted">Patient inconnu</span>
                                @endif
                            </td>
                            <td>{{ $consultation->titre }}</td>
                            <td>{{ Str::limit($consultation->diagnostic, 40) }}</td>
                            <td>
                                @if($consultation->ordonnance)
                                    <span title="{{ $consultation->ordonnance->contenu }}">
                                        {{ Str::limit($consultation->ordonnance->contenu, 40) }}
                                    </span>
                                @else
                                    <span class="text-muted" style="font-style: italic; font-size: 0.85rem;">Aucune ordonnance</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('medecin.consultation.show', $consultation->id) }}"><button class="btn-action view-dossier" style="cursor: not-allowed; opacity: 0.7;">
                                    <i class="fas fa-eye"></i> Détails
                                </button></a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="empty-row">
                                <i class="fas fa-folder-open"></i> Aucune consultation enregistrée.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="pagination-links">
                {{ $consultations->links() }}
            </div>
        </div>

    </main>
</div>

</body>
</html>