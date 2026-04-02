<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\MedecinController;

// Home Page
Route::get('/', function () {
    if (Auth::guard('web')->check()) {
        return redirect()->route('patient.dashboard');
    }
    if (Auth::guard('medecin')->check()) {
        return redirect()->route('medecin.dashboard');
    }
    return view('index');
})->name('home');

//Guest 
Route::middleware('guest')->group(function () {
    Route::get('/inscription', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/inscription', [AuthController::class, 'register']);

    Route::get('/connexion', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/connexion', [AuthController::class, 'login']);

    Route::post('/connexion/medecin', [AuthController::class, 'loginMedecin'])->name('login.medecin');


});

//user part
Route::middleware('auth')->group(function () {

    Route::get('/email/verify', [AuthController::class, 'verifyNotice'])->name('verification.notice');
    Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])->middleware('signed')->name('verification.verify');
    Route::post('/email/verification-notification', [AuthController::class, 'resendEmail'])->middleware('throttle:6,1')->name('verification.send');

    Route::get('/mon-espace', [PatientController::class, 'dashboard'])
        ->middleware(['auth', 'verified'])
        ->name('patient.dashboard');

    Route::get('/mon-espace/dossier-medical', [PatientController::class, 'dossierMedical'])
        ->middleware(['auth', 'verified'])
        ->name('patient.dossier');

    Route::get('/mon-espace/ordonnances', [PatientController::class, 'ordonnances'])
        ->middleware(['auth', 'verified'])
        ->name('patient.ordonnances');

    Route::get('/mon-espace/rendez-vous', [PatientController::class, 'rendezVous'])
        ->middleware(['auth', 'verified'])
        ->name('patient.rendezvous');

    Route::post('/mon-espace/rendez-vous', [PatientController::class, 'storeRendezVous'])
        ->middleware(['auth', 'verified'])
        ->name('rendezvous.store');

    Route::post('/mon-espace/rendez-vous/{id}/annuler', [PatientController::class, 'cancelRendezVous'])
        ->middleware(['auth', 'verified'])
        ->name('rendezvous.cancel');

});

Route::prefix('medecin')->name('medecin.')->middleware(['auth:medecin'])->group(function () {
    
    Route::get('/dashboard', [MedecinController::class, 'dashboard'])->name('dashboard');
    Route::get('/patients', [MedecinController::class, 'patients'])->name('patients');
    Route::get('/planning', [MedecinController::class, 'planning'])->name('planning');
    Route::get('/consultations', [MedecinController::class, 'listConsultations'])->name('consultations.list');
    Route::get('/consultation/create/{id}', [MedecinController::class, 'createConsultation'])->name('consultation.create');
    Route::post('/consultation/store/{id}', [MedecinController::class, 'storeConsultation'])->name('consultation.store');
    Route::get('/patients/{id}/dossier', [MedecinController::class, 'showDossier'])->name('patients.dossier');
    Route::get('/consultation/{id}/details', [MedecinController::class, 'showConsultation'])->name('consultation.show');
    Route::get('/rendez-vous/create', [MedecinController::class, 'createRendezVous'])->name('rdv.create');
    Route::post('/rendez-vous/store', [MedecinController::class, 'storeRendezVous'])->name('rdv.store');

});

    Route::post('/deconnexion', [AuthController::class, 'logout'])->name('logout');