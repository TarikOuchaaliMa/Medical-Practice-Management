<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Medecin;
use App\Models\RendezVous;
use App\Models\Consultation;
use App\Models\Ordonnance;

class TestSeeder extends Seeder
{
    public function run()
    {
        // 1. Nettoyage des tables (pour éviter les doublons lors des tests)
        // On désactive les clés étrangères temporairement pour pouvoir vider
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Ordonnance::truncate();
        Consultation::truncate();
        RendezVous::truncate();
        Medecin::truncate();
        User::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        echo "--- Démarrage du Seeder ---\n";

        // ---------------------------------------------------------
        // 2. CRÉATION DU MÉDECIN (Selon vos données)
        // ---------------------------------------------------------
        $medecin = Medecin::create([
            'nom' => 'El Halimi',
            'prenom' => 'Al Warda',
            'email' => 'Dr.ElHalimi.AlWarda@gmail.com',
            'password' => Hash::make('password'), // Mot de passe pour se connecter : password
            'specialite' => 'Généraliste',
            'telephone_pro' => '0522123456',
        ]);

        echo "✅ Médecin créé : Dr. El Halimi Al Warda (Mdp: password)\n";

        // ---------------------------------------------------------
        // 3. CRÉATION DES PATIENTS
        // ---------------------------------------------------------
        $patient1 = User::create([
            'nom' => 'Benali', 'prenom' => 'Karim',
            'email' => 'karim@test.com', 'password' => Hash::make('password'),
            'telephone' => '0661111111', 'date_naissance' => '1985-03-15',
            'groupe_sanguin' => 'A+', 'allergies_connues' => 'Pénicilline'
        ]);

        $patient2 = User::create([
            'nom' => 'Mansouri', 'prenom' => 'Layla',
            'email' => 'layla@test.com', 'password' => Hash::make('password'),
            'telephone' => '0662222222', 'date_naissance' => '1992-07-20',
            'groupe_sanguin' => 'O-', 'allergies_connues' => null
        ]);

        echo "✅ Patients créés : Karim et Layla (Mdp: password)\n";

        // ---------------------------------------------------------
        // 4. SCÉNARIOS DE TESTS
        // ---------------------------------------------------------

        // --- CAS A : Consultation Terminée + Ordonnance ACTIVE (En cours) ---
        // Utile pour : Tester l'onglet "Traitements en cours" chez le patient
        $rdv1 = RendezVous::create([
            'medecin_id' => $medecin->id,
            'user_id' => $patient1->id, // Karim
            'date_heure' => Carbon::now()->subDays(2), // Il y a 2 jours
            'motif' => 'Bronchite aigue',
            'statut' => 'termine'
        ]);

        $consult1 = Consultation::create([
            'medecin_id' => $medecin->id,
            'rendez_vous_id' => $rdv1->id,
            'titre' => 'Infection respiratoire',
            'diagnostic' => 'Bronchite avec fièvre légère.',
            'notes_privees' => 'Patient fumeur.'
        ]);

        Ordonnance::create([
            'consultation_id' => $consult1->id,
            'contenu' => "- Amoxicilline 1g (Matin et Soir pendant 7 jours)\n- Paracétamol 1g (Si fièvre)",
            'est_active' => 1,
            'date_fin' => Carbon::now()->addDays(5) // Fin dans 5 jours (donc actif)
        ]);

        
        // --- CAS B : Consultation Terminée + Ordonnance EXPIRÉE (Historique) ---
        // Utile pour : Tester l'onglet "Historique" chez le patient
        $rdv2 = RendezVous::create([
            'medecin_id' => $medecin->id,
            'user_id' => $patient1->id, // Karim
            'date_heure' => Carbon::now()->subMonths(1), // Il y a 1 mois
            'motif' => 'Douleurs estomac',
            'statut' => 'termine'
        ]);

        $consult2 = Consultation::create([
            'medecin_id' => $medecin->id,
            'rendez_vous_id' => $rdv2->id,
            'titre' => 'Gastrite',
            'diagnostic' => 'Inflammation de l\'estomac due au stress.',
            'notes_privees' => null
        ]);

        Ordonnance::create([
            'consultation_id' => $consult2->id,
            'contenu' => "- Gaviscon (Après repas)\n- Oméprazole 20mg",
            'est_active' => 1, // Était active
            'date_fin' => Carbon::now()->subDays(15) // Finie il y a 15 jours (donc expirée)
        ]);


        // --- CAS C : RDV Aujourd'hui (Confirmé) ---
        // Utile pour : Tester le Dashboard Médecin et le bouton "Démarrer"
        RendezVous::create([
            'medecin_id' => $medecin->id,
            'user_id' => $patient2->id, // Layla
            'date_heure' => Carbon::now()->addHours(2), // Aujourd'hui dans 2h
            'motif' => 'Consultation de suivi',
            'statut' => 'confirme'
        ]);


        // --- CAS D : RDV Futur (En attente) ---
        // Utile pour : Tester le Planning futur
        RendezVous::create([
            'medecin_id' => $medecin->id,
            'user_id' => $patient2->id, // Layla
            'date_heure' => Carbon::now()->addDays(3)->setHour(10)->setMinute(0),
            'motif' => 'Certificat médical',
            'statut' => 'en_attente'
        ]);


        // --- CAS E : RDV Annulé ---
        // Utile pour : Voir les badges rouges
        RendezVous::create([
            'medecin_id' => $medecin->id,
            'user_id' => $patient1->id, // Karim
            'date_heure' => Carbon::now()->subDays(5),
            'motif' => 'Urgence dentaire (annulé)',
            'statut' => 'annule'
        ]);

        echo "--- Seeder terminé avec succès ! ---\n";
    }
}