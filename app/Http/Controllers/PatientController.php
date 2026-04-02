<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\RendezVous;
use App\Models\Medecin;
use Carbon\Carbon;

class PatientController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();

        $nextRdv = $user->rendezVous()
                        ->where('date_heure', '>', now())
                        ->where('statut', '!=', 'annule')
                        ->orderBy('date_heure', 'asc')
                        ->with('medecin')
                        ->first();

        $activeOrdonnances = 0;
        if($user->consultations) {
            foreach ($user->consultations as $consultation) {
                if ($consultation->ordonnance && $consultation->ordonnance->est_active) {
                    $activeOrdonnances++;
                }
            }
        }

        $historique = $user->rendezVous()
                           ->orderBy('date_heure', 'desc')
                           ->take(5)
                           ->with('medecin')
                           ->get();

        $consultationsCount = $user->consultations()->count();

        return view('patient.dashboard', compact('user', 'nextRdv', 'activeOrdonnances', 'historique', 'consultationsCount'));
    }
    public function dossierMedical()
    {
        $user = Auth::user();
        $consultations = $user->consultations()
                              ->with(['medecin', 'ordonnance'])
                              ->orderBy('created_at', 'desc')
                              ->get();

        return view('patient.dossier', compact('user', 'consultations'));
    }
    public function ordonnances()
    {
        $user = Auth::user();
        $now = \Carbon\Carbon::now();

        $allConsultations = \App\Models\Consultation::whereHas('rendezVous', function($q) use ($user) {
                                    $q->where('user_id', $user->id);
                                })
                                ->has('ordonnance')
                                ->with(['ordonnance', 'rendezVous.medecin'])
                                ->orderBy('created_at', 'desc')
                                ->get();
        $actives = $allConsultations->filter(function ($c) use ($now) {
            $fin = $c->ordonnance->date_fin;
            if ($fin) {
                return $now->lte($fin);
            }
            return $c->created_at->diffInDays($now) <= 14;
        });
        $archives = $allConsultations->filter(function ($c) use ($now) {
            $fin = $c->ordonnance->date_fin;
            if ($fin) {
                return $now->gt($fin);
            }
            return $c->created_at->diffInDays($now) > 14;
        });

        return view('patient.ordonnances', compact('user', 'actives', 'archives'));
    }
    public function rendezVous()
    {
        $user = Auth::user();
        $medecins = Medecin::all();


        $takenSlots = RendezVous::where('date_heure', '>=', now())
                                ->where('statut', '!=', 'annule')
                                ->get()
                                ->map(function ($rdv) {
                                    return Carbon::parse($rdv->date_heure)->format('Y-m-d H:i');
                                });

        $upcoming = $user->rendezVous()
                         ->where('date_heure', '>=', now())
                         ->orderBy('date_heure', 'asc')
                         ->with('medecin')
                         ->get();

        $past = $user->rendezVous()
                     ->where('date_heure', '<', now())
                     ->orderBy('date_heure', 'desc')
                     ->with('medecin')
                     ->get();

        return view('patient.rendezvous', compact('user', 'medecins', 'upcoming', 'past', 'takenSlots'));
    }

    public function storeRendezVous(Request $request)
    {
    
        $minDate = now()->addHours(12);
        $maxDate = now()->addMonth();

        $request->validate([
            'medecin_id' => 'required|exists:medecins,id',
            'date' => 'required|date',
            'time' => 'required', 
        ]);


        $fullDate = $request->date . ' ' . $request->time;
        $carbonDate = Carbon::parse($fullDate);

        if ($carbonDate->lt($minDate)) {
            return back()->withErrors(['date' => 'Le rendez-vous doit être pris au moins 12h à l\'avance.']);
        }


        if ($carbonDate->gt($maxDate)) {
            return back()->withErrors(['date' => 'Vous ne pouvez pas réserver plus d\'un mois à l\'avance.']);
        }

        $isTaken = RendezVous::where('date_heure', $fullDate)
                             ->where('statut', '!=', 'annule')
                             ->exists();

        if ($isTaken) {
            return back()->withErrors(['time' => 'Ce créneau est déjà réservé par un autre patient.']);
        }

        RendezVous::create([
            'user_id' => Auth::id(),
            'medecin_id' => $request->medecin_id,
            'date_heure' => $fullDate,
            'motif' => $request->motif,
            'statut' => 'en_attente'
        ]);

        return redirect()->back()->with('success', 'Rendez-vous confirmé.');
    }

    public function cancelRendezVous($id)
    {
        $rdv = RendezVous::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
        
        if($rdv->date_heure < now()) {
            return back()->withErrors('Impossible d\'annuler un rendez-vous passé.');
        }

        $rdv->statut = 'annule';
        $rdv->save();

        return back()->with('success', 'Rendez-vous annulé.');
    }
}