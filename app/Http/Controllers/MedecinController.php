<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\RendezVous;
use App\Models\User;
use App\Models\Medecin;
use Carbon\Carbon;

class MedecinController extends Controller
{
    public function dashboard()
    {
        $medecin = Auth::guard('medecin')->user();
        $rdvTodayCount = RendezVous::where('medecin_id', $medecin->id)
                                   ->whereDate('date_heure', Carbon::today())
                                   ->where('statut', '!=', 'annule')
                                   ->count();

        $rdvPendingCount = RendezVous::where('medecin_id', $medecin->id)
                                     ->where('statut', 'en_attente')
                                     ->where('date_heure', '>', now())
                                     ->count();
        $totalPatients = RendezVous::where('medecin_id', $medecin->id)
                                   ->distinct('user_id')
                                   ->count('user_id');
        $todaysRdvs = RendezVous::where('medecin_id', $medecin->id)
                                ->whereDate('date_heure', Carbon::today())
                                ->where('statut', '!=', 'annule')
                                ->orderBy('date_heure', 'asc')
                                ->with('patient')
                                ->get();

        return view('medecin.dashboard', compact('medecin', 'rdvTodayCount', 'rdvPendingCount', 'totalPatients', 'todaysRdvs'));
    }
    public function patients(Request $request)
    {
        $medecin = Auth::guard('medecin')->user();
        $patientIds = RendezVous::where('medecin_id', $medecin->id)
                                ->distinct()
                                ->pluck('user_id');
        $query = User::whereIn('id', $patientIds)
                     ->orderBy('nom', 'asc');
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nom', 'like', '%' . $search . '%')
                  ->orWhere('prenom', 'like', '%' . $search . '%')
                  ->orWhere('telephone', 'like', '%' . $search . '%');
            });
        }
        $patients = $query->paginate(10)->appends(['search' => $request->search]);

        return view('medecin.patients', compact('medecin', 'patients'));
    }
    public function planning()
    {
        $medecin = Auth::guard('medecin')->user();
        $rendezVous = RendezVous::where('medecin_id', $medecin->id)
                                ->orderBy('date_heure','desc')
                                ->with('patient')
                                ->paginate(15);

        return view('medecin.planning', compact('medecin', 'rendezVous'));
    }

    public function listConsultations()
    {
        $medecin = Auth::guard('medecin')->user();

        $consultations = \App\Models\Consultation::where('medecin_id', $medecin->id)
                                ->with(['rendezVous.patient', 'ordonnance'])
                                ->orderBy('created_at', 'desc')
                                ->paginate(10);

        return view('medecin.consultations', compact('medecin', 'consultations'));
    }

    public function createConsultation($id)
    {
        $rdv = \App\Models\RendezVous::with('patient')->findOrFail($id);

        return view('medecin.consultation_create', compact('rdv'));
    }

    public function storeConsultation(Request $request, $id)
    {
        $request->validate([
            'titre' => 'required|string',
            'diagnostic' => 'required|string',
            'traitement' => 'nullable|string',
            'date_fin' => 'nullable|date|after_or_equal:today', 
        ]);
        $consultation = \App\Models\Consultation::create([
            'medecin_id' => Auth::guard('medecin')->id(),
            'rendez_vous_id' => $id,
            'titre' => $request->titre,
            'diagnostic' => $request->diagnostic,
            'notes_privees' => $request->notes_privees,
        ]);
        if ($request->filled('traitement')) {
            \App\Models\Ordonnance::create([
                'consultation_id' => $consultation->id,
                'contenu' => $request->traitement,
                'est_active' => 1,
                'date_fin' => $request->date_fin, 
            ]);
        }
        $rdv = \App\Models\RendezVous::find($id);
        $rdv->statut = 'termine';
        $rdv->save();

        return redirect()->route('medecin.planning')->with('success', 'Consultation enregistrée.');
    }
    public function showDossier($id)
    {
        $patient = \App\Models\User::findOrFail($id);

        $consultations = \App\Models\Consultation::whereHas('rendezVous', function($q) use ($id) {
            $q->where('user_id', $id);
        })
        ->with('ordonnance')
        ->orderBy('created_at', 'desc')
        ->get();

        $appointments = \App\Models\RendezVous::where('user_id', $id)
                                              ->orderBy('date_heure', 'desc')
                                              ->get();

        return view('medecin.patients_dossier', compact('patient', 'consultations', 'appointments'));
    }

    public function showConsultation($id)
    {

        $consultation = \App\Models\Consultation::with(['rendezVous.patient', 'ordonnance'])->findOrFail($id);
        return view('medecin.consultation_show', compact('consultation'));
    }

    public function createRendezVous()
    {
        $medecin = Auth::guard('medecin')->user();
        
        $patients = \App\Models\User::orderBy('nom')->get();
        $takenSlots = \App\Models\RendezVous::where('medecin_id', $medecin->id)
                                ->where('date_heure', '>=', now())
                                ->where('statut', '!=', 'annule')
                                ->get()
                                ->map(function ($rdv) {
                                    return \Carbon\Carbon::parse($rdv->date_heure)->format('Y-m-d H:i');
                                });
        
        return view('medecin.rdv_create', compact('patients', 'takenSlots'));
    }

    public function storeRendezVous(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'date' => 'required|date',
            'time' => 'required',
            'motif' => 'required|string|max:255',
        ]);


        $fullDate = $request->date . ' ' . $request->time;
        $carbonDate = \Carbon\Carbon::parse($fullDate);


        $minDate = now()->addHours(12); 
        $maxDate = now()->addMonth();  


        if ($carbonDate->lt($minDate)) {
            return back()->withErrors(['time' => 'Le rendez-vous doit être pris au moins 12h à l\'avance.'])->withInput();
        }

 
        if ($carbonDate->gt($maxDate)) {
            return back()->withErrors(['date' => 'Vous ne pouvez pas planifier plus d\'un mois à l\'avance.'])->withInput();
        }

        $isTaken = \App\Models\RendezVous::where('medecin_id', Auth::guard('medecin')->id())
                                         ->where('date_heure', $fullDate)
                                         ->where('statut', '!=', 'annule')
                                         ->exists();

        if ($isTaken) {
            return back()->withErrors(['time' => 'Ce créneau est déjà réservé.'])->withInput();
        }

        \App\Models\RendezVous::create([
            'medecin_id' => Auth::guard('medecin')->id(),
            'user_id' => $request->user_id,
            'date_heure' => $fullDate,
            'motif' => $request->motif,
            'statut' => 'confirme', 
        ]);

        return redirect()->route('medecin.planning')->with('success', 'Rendez-vous ajouté avec succès.');
    }
    
}