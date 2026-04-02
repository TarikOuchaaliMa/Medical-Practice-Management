<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Dossier Médical - Cabinet Dr. El Halimi</title>
    <link href="{{ asset('css/patient/dossier.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<div class="page-wrapper">
    
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
            <a href="{{ route('patient.dashboard') }}"><i class="fas fa-home"></i> Tableau de bord</a>
            <a href="{{ route('patient.rendezvous') }}"><i class="fas fa-calendar"></i> Rendez-vous</a>
            <a href="{{ route('patient.dossier') }}" class="active"><i class="fas fa-file-medical"></i> Dossier Médical</a>
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
        
        <header class="page-header">
            <div>
                <h1>Dossier Médical 📂</h1>
                <p>Historique complet de vos soins et informations vitales.</p>
            </div>
        </header>

        <section class="medical-summary">
            <h3 class="section-title">Fiche Santé</h3>
            
            <div class="summary-grid">
                <div class="summary-card danger">
                    <div class="card-icon"><i class="fas fa-tint"></i></div>
                    <div class="card-info">
                        <span class="label">Groupe Sanguin</span>
                        <span class="value">{{ $user->groupe_sanguin ?? '--' }}</span>
                    </div>
                </div>

                <div class="summary-card warning">
                    <div class="card-icon"><i class="fas fa-exclamation-triangle"></i></div>
                    <div class="card-info">
                        <span class="label">Allergies Connues</span>
                        <span class="value small">{{ $user->allergies_connues ?? 'Aucune allergie' }}</span>
                    </div>
                </div>

                <div class="summary-card info">
                    <div class="card-icon"><i class="fas fa-user-md"></i></div>
                    <div class="card-info">
                        <span class="label">Statut Actuel</span>
                        <span class="status-pill">{{ $user->statut_medical }}</span>
                    </div>
                </div>
            </div>
        </section>

        <section class="history-section">
            <h3 class="section-title">Historique des Consultations</h3>
            
            <div class="timeline">
                @forelse($consultations as $consultation)
                    <div class="timeline-item">
                        <div class="timeline-date">
                            <span class="day">{{ $consultation->created_at->format('d') }}</span>
                            <span class="month">{{ $consultation->created_at->format('M') }}</span>
                            <span class="year">{{ $consultation->created_at->format('Y') }}</span>
                        </div>
                        
                        <div class="timeline-content">
                            <div class="timeline-header">
                                <h4>{{ $consultation->titre }}</h4>
                                <span class="doctor-name">Dr. {{ $consultation->medecin->nom }}</span>
                            </div>
                            
                            <div class="timeline-body">
                                <div class="info-block">
                                    <strong>Diagnostic :</strong>
                                    <p>{{ $consultation->diagnostic }}</p>
                                </div>
                                
                                @if($consultation->traitement)
                                    <div class="info-block">
                                        <strong>Traitement :</strong>
                                        <p>{{ $consultation->traitement }}</p>
                                    </div>
                                @endif

                                @if($consultation->ordonnance)
                                    <div class="prescription-badge">
                                        <i class="fas fa-prescription"></i> Ordonnance incluse
                                        @if($consultation->ordonnance->est_active)
                                            <span class="active-dot">● En cours</span>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="empty-state">
                        <i class="fas fa-folder-open"></i>
                        <p>Aucune consultation trouvée.</p>
                    </div>
                @endforelse
            </div>
        </section>

    </main>
</div>

</body>
</html>