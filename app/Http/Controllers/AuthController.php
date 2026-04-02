<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

class AuthController extends Controller
{
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'nom' => ['required', 'string', 'regex:/^[a-zA-ZÀ-ÿ\s]+$/', 'max:50'],
            'prenom' => ['required', 'string', 'regex:/^[a-zA-ZÀ-ÿ\s]+$/', 'max:50'],
            'telephone' => ['required', 'digits:10'], 
            'date_naissance' => ['required', 'date', 'before:-18 years', 'after:-100 years'],
            'adresse' => ['required', 'string', 'max:500'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
            'groupe_sanguin' => ['nullable', 'string', 'max:5'],
            'allergies_connues' => ['nullable', 'string'],
        ], [
            'nom.regex' => 'Le nom ne doit contenir que des lettres et des espaces.',
            'prenom.regex' => 'Le prénom ne doit contenir que des lettres et des espaces.',
            'telephone.digits' => 'Le numéro de téléphone doit contenir exactement 10 chiffres.',
            'date_naissance.before' => 'Vous devez avoir au moins 18 ans pour vous inscrire.',
            'date_naissance.after' => 'Date de naissance invalide (trop ancienne).',
            'password.confirmed' => 'Les mots de passe ne correspondent pas.',
        ]);

        $user = User::create([
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'email' => $request->email,
            'telephone' => $request->telephone,
            'date_naissance' => $request->date_naissance,
            'groupe_sanguin' => $request->groupe_sanguin,
            'allergies_connues' => $request->allergies_connues,
            'adresse' => $request->adresse,
            'password' => Hash::make($request->password),
            'statut_medical' => 'Suivi requis',
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect()->route('verification.notice'); 
    }
    public function verifyNotice()
    {
        return view('auth.verify-email');
    }

    public function verifyEmail(EmailVerificationRequest $request)
    {
        $request->fulfill();
        return redirect()->route('patient.dashboard')->with('success', 'Email vérifié !');
    }


    public function resendEmail(Request $request)
    {
        $request->user()->sendEmailVerificationNotification();
        return back()->with('status', 'verification-link-sent');
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);
        if (Auth::guard('web')->attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended(route('patient.dashboard'));
        }
        return back()->withErrors([
            'email' => 'Les identifiants fournis ne correspondent pas à nos enregistrements.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        if (Auth::guard('medecin')->check()) {
            Auth::guard('medecin')->logout();
        }
        if (Auth::guard('web')->check()) {
            Auth::guard('web')->logout();
        }
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }

    public function loginMedecin(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::guard('medecin')->attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended(route('medecin.dashboard'));
        }

        return back()->withErrors([
            'email' => 'Identifiants médecin incorrects.',
        ])->onlyInput('email');
    }
}