<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mon Espace - Cabinet Dr. El Halimi</title>
    <link href="{{ asset('css/patient/dashboard.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<div class="dashboard-wrapper">
    
    <aside class="sidebar">
        <div class="sidebar-brand">
            <i class="fas fa-heartbeat"></i> Cabinet Dr. El Halimi
        </div>

        <div class="user-box">
            <div class="user-avatar">
                {{ substr($user->prenom, 0, 1) }}{{ substr($user->nom, 0, 1) }}
            </div>
            <div class="user-details">
                <strong>{{ $user->prenom }} {{ $user->nom }}</strong>
                <span>Patient</span>
            </div>
        </div>

        <nav class="nav-links">
            <a href="{{ route('patient.dashboard') }}" class="active"><i class="fas fa-home"></i> Tableau de bord</a>
            <a href="{{ route('patient.rendezvous') }}"><i class="fas fa-calendar"></i> Rendez-vous</a>
            <a href="{{ route('patient.dossier') }}"><i class="fas fa-file-medical"></i> Dossier Médical</a>
            <a href="{{ route('patient.ordonnances') }}"><i class="fas fa-pills"></i> Ordonnances</a>
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

    <main class="main-content">
        
        <header class="top-header">
            <div>
                <h1>Bonjour, {{ $user->prenom }} 👋</h1>
                <p>Voici le résumé de votre santé.</p>
            </div>
            <a href="{{ route('patient.rendezvous') }}"><button class="btn-new-rdv">
                <i class="fas fa-plus"></i> Prendre RDV
            </button></a>
        </header>

        <div class="stats-container">
            
            <div class="stat-card blue">
                <div class="card-top">
                    <div class="icon-box"><i class="fas fa-calendar-check"></i></div>
                    <span>Prochain RDV</span>
                </div>
                @if($nextRdv)
                    <div class="card-value">
                        {{ \Carbon\Carbon::parse($nextRdv->date_heure)->format('d M à H:i') }}
                    </div>
                    <div class="card-sub">Dr. {{ $nextRdv->medecin->nom }}</div>
                    <div style="margin-top:5px;">
                        <span class="badge-specialite">{{ $nextRdv->medecin->specialite }}</span>
                    </div>
                @else
                    <div class="card-value">--</div>
                    <div class="card-sub">Aucun rendez-vous prévu</div>
                @endif
            </div>

            <div class="stat-card purple">
                <div class="card-top">
                    <div class="icon-box"><i class="fas fa-prescription-bottle"></i></div>
                    <span>Ordonnances</span>
                </div>
                <div class="card-value">{{ $activeOrdonnances }}</div>
                <div class="card-sub">Traitements actifs en cours</div>
            </div>

            <div class="stat-card green">
                <div class="card-top">
                    <div class="icon-box"><i class="fas fa-file-medical-alt"></i></div>
                    <span>Consultations</span>
                </div>
                <div class="card-value">{{ $consultationsCount }}</div>
                <div class="card-sub">Total effectué</div>
            </div>

        </div>

        <div class="content-grid">
            
            <div class="section-box">
                <h3>Historique des Rendez-vous</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Médecin</th>
                            <th>Date</th>
                            <th>Motif</th>
                            <th>Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($historique as $rdv)
                        <tr>
                            <td>Dr. {{ $rdv->medecin->nom }}</td>
                            <td>{{ \Carbon\Carbon::parse($rdv->date_heure)->format('d/m/Y') }}</td>
                            <td>{{ $rdv->motif }}</td>
                            <td>
                                <span class="status {{ $rdv->statut }}">
                                    {{ ucfirst(str_replace('_', ' ', $rdv->statut)) }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

        </div>

    </main>
</div>

</body>
</html>