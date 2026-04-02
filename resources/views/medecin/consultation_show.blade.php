<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Détails Consultation #{{ $consultation->id }}</title>
    <link href="{{ asset('css/medecin/consultation_show.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<div class="report-container">
    
    {{-- Header du rapport --}}
    <div class="report-header">
        <div class="doc-info">
            <h1>Dr. {{ Auth::guard('medecin')->user()->nom }}</h1>
            <p>{{ Auth::guard('medecin')->user()->specialite }}</p>
        </div>
        <div class="date-info">
            <span>Date: {{ $consultation->created_at->format('d/m/Y') }}</span>
            <span class="ref">Réf: CS-{{ $consultation->id }}</span>
        </div>
    </div>

    <hr class="divider">

    {{-- Info Patient --}}
    <div class="patient-section">
        <label>PATIENT :</label>
        <h2>{{ $consultation->rendezVous->patient->nom }} {{ $consultation->rendezVous->patient->prenom }}</h2>
        <p>Né(e) le : {{ \Carbon\Carbon::parse($consultation->rendezVous->patient->date_naissance)->format('d/m/Y') }}</p>
    </div>

    {{-- Contenu du rapport --}}
    <div class="report-body">
        
        <div class="section">
            <h3><i class="fas fa-heading"></i> Motif / Titre</h3>
            <div class="content-box">
                {{ $consultation->titre }}
            </div>
        </div>

        <div class="section">
            <h3><i class="fas fa-stethoscope"></i> Diagnostic</h3>
            <div class="content-box">
                {{ $consultation->diagnostic }}
            </div>
        </div>

        <div class="section">
            <h3><i class="fas fa-pills"></i> Traitement / Ordonnance</h3>
            <div class="content-box">
                @if($consultation->ordonnance)
                    <div style="margin-bottom: 15px; padding-bottom: 10px; border-bottom: 1px dashed #e2e8f0; color: #059669; font-weight: 600; font-size: 0.9rem;">
                        @if($consultation->ordonnance->date_fin)
                            <i class="far fa-calendar-check"></i> Traitement jusqu'au : 
                            {{ \Carbon\Carbon::parse($consultation->ordonnance->date_fin)->format('d/m/Y') }}
                        @else
                            <i class="far fa-clock"></i> Durée standard (Non spécifiée)
                        @endif
                    </div>

                    {{-- Affichage du Contenu --}}
                    {!! nl2br(e($consultation->ordonnance->contenu)) !!}

                @else
                    <span class="text-muted">Aucune ordonnance délivrée pour cette consultation.</span>
                @endif
            </div>
        </div>

        {{-- Notes Privées (Visible seulement par le médecin) --}}
        @if($consultation->notes_privees)
        <div class="section private-section">
            <h3><i class="fas fa-lock"></i> Notes Privées (Confidentiel)</h3>
            <div class="content-box private-box">
                {!! nl2br(e($consultation->notes_privees)) !!}
            </div>
        </div>
        @endif

    </div>

    {{-- Pied de page / Actions --}}
    <div class="report-footer">
        <a href="{{ url()->previous() }}" class="btn-back">
            <i class="fas fa-arrow-left"></i> Retour
        </a>
        <button onclick="window.print()" class="btn-print">
            <i class="fas fa-print"></i> Imprimer
        </button>
    </div>

</div>

</body>
</html>