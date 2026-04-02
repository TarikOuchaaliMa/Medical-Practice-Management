<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Dossier Médical - {{ $patient->nom }}</title>
    <link href="{{ asset('css/medecin/dossier.css') }}" rel="stylesheet">
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
            <a href="{{ route('medecin.dashboard') }}"><i class="fas fa-th-large"></i> Tableau de bord</a>
            <a href="{{ route('medecin.patients') }}" class="active"><i class="fas fa-users"></i> Mes Patients</a>
            <a href="{{ route('medecin.planning') }}"><i class="fas fa-calendar-alt"></i> Planning</a>
            <a href="{{ route('medecin.consultations.list') }}"><i class="fas fa-file-medical-alt"></i> Consultations</a>
        </nav>
        <div class="sidebar-footer">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn-logout"><i class="fas fa-sign-out-alt"></i> Déconnexion</button>
            </form>
        </div>
    </aside>

    {{-- MAIN CONTENT --}}
    <main class="main-content">
        
        <header class="top-bar">
            <h1>Dossier Médical</h1>
            <a href="{{ route('medecin.patients') }}" class="btn-back"><i class="fas fa-arrow-left"></i> Retour liste</a>
        </header>

        <div class="dossier-grid">
            
            {{-- LEFT COLUMN: PATIENT INFO --}}
            <div class="left-col">
                <div class="card patient-card">
                    <div class="avatar-large">{{ substr($patient->prenom, 0, 1) }}</div>
                    <h2>{{ $patient->nom }} {{ $patient->prenom }}</h2>
                    <p class="text-muted">Patient #{{ $patient->id }}</p>

                    <div class="info-list">
                        <div class="info-item">
                            <i class="fas fa-birthday-cake"></i>
                            <span>{{ \Carbon\Carbon::parse($patient->date_naissance)->age }} ans ({{ \Carbon\Carbon::parse($patient->date_naissance)->format('d/m/Y') }})</span>
                        </div>
                        <div class="info-item">
                            <i class="fas fa-phone"></i>
                            <span>{{ $patient->telephone }}</span>
                        </div>
                        <div class="info-item">
                            <i class="fas fa-envelope"></i>
                            <span>{{ $patient->email }}</span>
                        </div>
                        <div class="info-item">
                            <i class="fas fa-tint"></i>
                            <span>Groupe: <strong>{{ $patient->groupe_sanguin ?? 'N/A' }}</strong></span>
                        </div>
                    </div>
                </div>

                <div class="card alert-card">
                    <h3><i class="fas fa-exclamation-triangle"></i> Allergies / Alertes</h3>
                    @if($patient->allergies_connues)
                        <p class="alert-text">{{ $patient->allergies_connues }}</p>
                    @else
                        <p class="safe-text">Aucune allergie signalée.</p>
                    @endif
                </div>
            </div>

            {{-- RIGHT COLUMN: HISTORY --}}
            <div class="right-col">
                
                {{-- QUICK ACTION --}}
                <div class="action-header">
                    <h3>Historique des Consultations</h3>
                </div>

                <div class="timeline">
                    @forelse($consultations as $consultation)
                        <div class="timeline-item">
                            <div class="timeline-date">
                                <span class="day">{{ $consultation->created_at->format('d') }}</span>
                                <span class="month">{{ $consultation->created_at->format('M') }}</span>
                                <span class="year">{{ $consultation->created_at->format('Y') }}</span>
                            </div>
                            <div class="timeline-content">
                                <h4 class="t-title">{{ $consultation->titre }}</h4>
                                
                                <div class="t-section">
                                    <strong>Diagnostic:</strong>
                                    <p>{{ $consultation->diagnostic }}</p>
                                </div>

                                @if($consultation->ordonnance)
                                    <div class="t-section" style="background: #f0fdf4; padding: 10px; border-radius: 6px; border-left: 3px solid #16a34a; margin-top: 5px;">
                                        <strong style="color: #166534;"><i class="fas fa-pills"></i> Ordonnance :</strong>
                                        @if($consultation->ordonnance->date_fin)
                                            <small style="display:block; color:#15803d; margin-bottom:4px;">
                                                Jusqu'au {{ \Carbon\Carbon::parse($consultation->ordonnance->date_fin)->format('d/m/Y') }}
                                            </small>
                                        @endif

                                        <p style="margin: 0; color: #14532d;">
                                            {{ $consultation->ordonnance->contenu }}
                                        </p>
                                    </div>
                                @endif

                                @if($consultation->notes_privees)
                                <div class="t-private">
                                    <i class="fas fa-lock"></i> <em>{{ $consultation->notes_privees }}</em>
                                </div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="empty-box">
                            <i class="fas fa-folder-open"></i>
                            <p>Aucune consultation enregistrée dans ce dossier.</p>
                        </div>
                    @endforelse
                </div>

            </div>
        </div>

    </main>
</div>
</body>
</html>