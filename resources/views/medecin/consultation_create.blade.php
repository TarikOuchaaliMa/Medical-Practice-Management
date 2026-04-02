<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouvelle Consultation - Espace Médecin</title>
    {{-- Lien vers le fichier CSS spécifique --}}
    <link href="{{ asset('css/medecin/consultation_create.css') }}" rel="stylesheet">
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
                {{-- On utilise le guard 'medecin' pour récupérer l'utilisateur connecté --}}
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
            {{-- Lien actif pour montrer qu'on est dans une sous-section consultation --}}
            <a href="#" class="active">
                <i class="fas fa-file-medical-alt"></i> Consultation
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
            <h1>Consultation en cours</h1>
        </header>

        <div class="section-container">
            
            {{-- BANNIÈRE INFO PATIENT --}}
            <div class="patient-banner">
                <div class="p-avatar">
                    <i class="fas fa-user"></i>
                </div>
                <div class="p-details">
                    <h2>{{ $rdv->patient->nom }} {{ $rdv->patient->prenom }}</h2>
                    <div class="p-meta">
                        <span><i class="fas fa-phone"></i> {{ $rdv->patient->telephone }}</span>
                        <span><i class="fas fa-calendar"></i> RDV de {{ \Carbon\Carbon::parse($rdv->date_heure)->format('H:i') }}</span>
                        <span><i class="fas fa-birthday-cake"></i> {{ \Carbon\Carbon::parse($rdv->patient->date_naissance)->age }} ans</span>
                    </div>
                </div>
            </div>

            {{-- FORMULAIRE DE CONSULTATION --}}
            {{-- On poste vers la route store avec l'ID du rendez-vous --}}
            <form method="POST" action="{{ route('medecin.consultation.store', $rdv->id) }}">
                @csrf

                {{-- 1. TITRE --}}
                <div class="form-group">
                    <label for="titre">Titre de la consultation *</label>
                    <input type="text" id="titre" name="titre" class="form-control" 
                           placeholder="Ex: Grippe, Suivi cardiologie..." 
                           value="{{ old('titre', $rdv->motif) }}" required>
                    @error('titre') <span class="error-msg">{{ $message }}</span> @enderror
                </div>

                {{-- 2. DIAGNOSTIC --}}
                <div class="form-group">
                    <label for="diagnostic">Diagnostic *</label>
                    <textarea id="diagnostic" name="diagnostic" class="form-control" rows="4" 
                              placeholder="Résultat de l'examen, observations cliniques..." required>{{ old('diagnostic') }}</textarea>
                    @error('diagnostic') <span class="error-msg">{{ $message }}</span> @enderror
                </div>

                <div class="row-split">
                    {{-- 3. TRAITEMENT --}}
                    <div class="form-group half">
                        <label for="traitement">Traitement (Visible par le patient)</label>
                        <textarea id="traitement" name="traitement" class="form-control" rows="6" 
                                  placeholder="Médicaments prescrits, conseils, repos...">{{ old('traitement') }}</textarea>
                        <div style="margin-top: 15px;">
                            <label for="date_fin" style="font-size: 0.9rem; color: #475569;">
                                <i class="far fa-calendar-times"></i> Date de fin du traitement (Optionnel)
                            </label>
                            <input type="date" id="date_fin" name="date_fin" class="form-control" 
                                min="{{ date('Y-m-d') }}" {{-- Empêche de choisir une date passée --}}
                                value="{{ old('date_fin') }}">
                        </div>
                    </div>

                    {{-- 4. NOTES PRIVEES --}}
                    <div class="form-group half">
                        <label for="notes_privees" style="color: #dc2626;">
                            <i class="fas fa-lock"></i> Notes Privées (Invisible pour le patient)
                        </label>
                        <textarea id="notes_privees" name="notes_privees" class="form-control private-bg" rows="6" 
                                  placeholder="Observations personnelles, doutes, rappels...">{{ old('notes_privees') }}</textarea>
                    </div>
                </div>

                <div class="form-actions">
                    {{-- Bouton Annuler renvoie au planning --}}
                    <a href="{{ route('medecin.planning') }}" class="btn-cancel">Annuler</a>
                    <button type="submit" class="btn-submit">
                        <i class="fas fa-check"></i> Terminer la consultation
                    </button>
                </div>

            </form>
        </div>

    </main>
</div>

</body>
</html>