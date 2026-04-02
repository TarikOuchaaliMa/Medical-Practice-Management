<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mes Ordonnances - Cabinet Dr. El Halimi</title>
    <link href="{{ asset('css/patient/ordonnances.css') }}" rel="stylesheet">
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
            <a href="{{ route('patient.rendezvous') }}"><i class="fas fa-calendar"></i> Rendez-vous</a>
            <a href="{{ route('patient.dossier') }}"><i class="fas fa-file-medical"></i> Dossier Médical</a>
            <a href="{{ route('patient.ordonnances') }}" class="active"><i class="fas fa-pills"></i> Ordonnances</a>
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
                <h1>Mes Ordonnances 💊</h1>
                <p>Gérez vos traitements actuels et passés.</p>
            </div>
        </header>

        <section class="ordo-section">
            <h3 class="section-title active-title">Traitements en cours</h3>
            
            <div class="ordo-flex-container">
                @forelse($actives as $consultation)
                    <div class="ordo-card active-card">
                        <div class="ordo-header">
                            <div class="header-left">
                                <div class="icon-wrapper"><i class="fas fa-prescription-bottle-alt"></i></div>
                                <div class="header-info">
                                    <span class="date">Depuis le {{ $consultation->ordonnance->created_at->format('d/m/Y') }}</span>
                                    <span class="doctor">Dr. {{ $consultation->medecin->nom }}</span>
                                </div>
                            </div>
                            <span class="status-badge">Actif</span>
                        </div>

                        <div class="ordo-body">
                            <p class="med-list">{!! nl2br(e($consultation->ordonnance->contenu)) !!}</p>
                        </div>

                        <div class="ordo-footer">
                            <div class="expiry">
                                <i class="far fa-clock"></i> 
                                @if($consultation->ordonnance->date_fin)
                                    @php
                                        $fin = \Carbon\Carbon::parse($consultation->ordonnance->date_fin);
                                        $joursRestants = now()->diffInDays($fin, false);
                                    @endphp

                                    @if($joursRestants > 0)
                                        Expire le {{ $fin->format('d/m/Y') }} (Reste {{ ceil($joursRestants) }}j)
                                    @else
                                        Expirée le {{ $fin->format('d/m/Y') }}
                                    @endif
                                @else
                                    Durée standard (15j)
                                @endif
                            </div>
                            
                            <button class="btn-download"><i class="fas fa-download"></i> PDF</button>
                        </div>
                    </div>
                @empty
                    <div class="empty-state">
                        <p>Aucun traitement actif.</p>
                    </div>
                @endforelse
            </div>
        </section>

        <section class="ordo-section">
            <h3 class="section-title history-title">Historique</h3>

            <div class="ordo-list-flex">
                @forelse($archives as $consultation)
                    <div class="ordo-row">
                        <div class="row-left">
                            <div class="date-box">
                                <span class="d">{{ $consultation->created_at->format('d') }}</span>
                                <span class="m">{{ $consultation->created_at->format('M') }}</span>
                            </div>
                            <div class="row-info">
                                <strong>{{ $consultation->titre }}</strong>
                                <span>Dr. {{ $consultation->medecin->nom }}</span>
                            </div>
                        </div>
                        <div class="row-right">
                            <span class="status-text">Terminé</span>
                            <button class="btn-icon"><i class="fas fa-eye"></i></button>
                        </div>
                    </div>
                @empty
                    <div class="empty-state">
                        <p>Aucun historique.</p>
                    </div>
                @endforelse
            </div>
        </section>

    </main>
</div>

</body>
</html>