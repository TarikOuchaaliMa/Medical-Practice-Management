<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Espace Médecin - Mes Patients</title>
    <link href="{{ asset('css/medecin/patients.css') }}" rel="stylesheet">
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
            <a href="{{ route('medecin.patients') }}" class="active">
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
            <h1>Liste de Mes Patients</h1>
        </header>

        {{-- PATIENT LIST SECTION --}}
        <div class="section-container">
            <div class="section-header">
                <h3>Patients suivis ({{ $patients->total() }} total)</h3>
                
                {{-- SEARCH BAR --}}
                <form method="GET" action="{{ route('medecin.patients') }}" class="search-form">
                    <input type="text" name="search" placeholder="Rechercher par Nom, Prénom ou Téléphone..." value="{{ request('search') }}" class="search-input">
                    <button type="submit" class="btn-secondary search-btn">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
            </div>

            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Dossier / Nom</th>
                        <th>Date de Naissance</th>
                        <th>Téléphone</th>
                        <th>Adresse E-mail</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($patients as $patient)
                        <tr>
                            <td>
                                <div class="patient-info">
                                    <span class="patient-id">{{ $patient->code_dossier }}</span>
                                    <strong>{{ $patient->nom }} {{ $patient->prenom }}</strong>
                                </div>
                            </td>
                            <td>{{ \Carbon\Carbon::parse($patient->date_naissance)->format('d/m/Y') }}</td>
                            <td>{{ $patient->telephone }}</td>
                            <td>{{ $patient->email }}</td>
                            <td>
                                <a href="{{ route('medecin.patients.dossier', $patient->id) }}" class="btn-action view-dossier" title="Voir le dossier">
                                    <i class="fas fa-file-alt"></i> Dossier
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="empty-row">
                                <i class="fas fa-users-slash"></i> Vous n'avez pas encore de patients enregistrés via RDV.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            {{-- PAGINATION LINKS --}}
            <div class="pagination-links">
                {{ $patients->links() }}
            </div>
        </div>
    </main>
</div>

</body>
</html>