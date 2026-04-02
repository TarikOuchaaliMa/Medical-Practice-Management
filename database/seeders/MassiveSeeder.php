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
use Faker\Factory as Faker;

class MassiveSeeder extends Seeder
{
    public function run()
    {
        // Configuration de Faker en Français
        $faker = Faker::create('fr_FR');

        // Nettoyage
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Ordonnance::truncate();
        Consultation::truncate();
        RendezVous::truncate();
        Medecin::truncate();
        User::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        echo "--- 🚀 Démarrage de la génération massive ---\n";

        // ---------------------------------------------------------
        // 1. LE MÉDECIN PRINCIPAL (Pour vous connecter)
        // ---------------------------------------------------------
        $medecin = Medecin::create([
            'nom' => 'El Halimi',
            'prenom' => 'Al Warda',
            'email' => 'Dr.ElHalimi.AlWarda@gmail.com',
            'password' => Hash::make('password'),
            'specialite' => 'Généraliste',
            'telephone_pro' => '0522123456',
        ]);

        echo "✅ Médecin créé (Login: Dr.ElHalimi.AlWarda@gmail.com / password)\n";

        // ---------------------------------------------------------
        // 2. GÉNÉRATION DE 50 PATIENTS
        // ---------------------------------------------------------
        $patientsIds = [];
        for ($i = 0; $i < 50; $i++) {
            $p = User::create([
                'nom' => $faker->lastName,
                'prenom' => $faker->firstName,
                'email' => $faker->unique()->safeEmail,
                'password' => Hash::make('password'),
                'telephone' => '06' . $faker->randomNumber(8, true),
                'date_naissance' => $faker->dateTimeBetween('-80 years', '-5 years')->format('Y-m-d'),
                'groupe_sanguin' => $faker->randomElement(['A+', 'A-', 'B+', 'B-', 'O+', 'O-', 'AB+', 'AB-']),
                'allergies_connues' => $faker->boolean(20) ? $faker->randomElement(['Pénicilline', 'Pollen', 'Arachides', 'Latex', 'Aspirine']) : null,
            ]);
            $patientsIds[] = $p->id;
        }
        echo "✅ 50 Patients générés.\n";

        // ---------------------------------------------------------
        // 3. GÉNÉRATION DE 300 RENDEZ-VOUS (Passés et Futurs)
        // ---------------------------------------------------------
        
        // Listes de données médicales pour varier les textes
        $motifs = ['Grippe', 'Fièvre', 'Consultation de suivi', 'Vaccination', 'Douleurs abdominales', 'Migraine', 'Certificat sport', 'Angine', 'Fatigue chronique', 'Hypertension'];
        $diagnostics = ['Rhinopharyngite virale', 'Gastro-entérite aiguë', 'Bronchite', 'État grippal', 'Bonne santé générale', 'Otite moyenne', 'Crise de migraine'];
        $medicaments = ["Doliprane 1000mg", "Amoxicilline 1g", "Spasfon", "Ibuprofène 400mg", "Gaviscon", "Vogalib", "Maxilase"];

        for ($i = 0; $i < 300; $i++) {
            
            // On choisit une date aléatoire entre il y a 6 mois et dans 2 mois
            $date = $faker->dateTimeBetween('-6 months', '+2 months');
            $carbonDate = Carbon::instance($date)->setHour(rand(9, 17))->setMinute($faker->randomElement([0, 15, 30, 45]));

            // Logique du statut selon la date
            if ($carbonDate->isPast()) {
                // Si c'est passé : soit terminé (80%), soit annulé (20%)
                $statut = $faker->boolean(80) ? 'termine' : 'annule';
            } else {
                // Si c'est futur : soit en attente (50%), soit confirmé (50%)
                $statut = $faker->boolean(50) ? 'en_attente' : 'confirme';
            }

            $rdv = RendezVous::create([
                'medecin_id' => $medecin->id,
                'user_id' => $faker->randomElement($patientsIds),
                'date_heure' => $carbonDate,
                'motif' => $faker->randomElement($motifs),
                'statut' => $statut
            ]);

            // ---------------------------------------------------------
            // 4. SI TERMINÉ -> CRÉER CONSULTATION + ORDONNANCE
            // ---------------------------------------------------------
            if ($statut == 'termine') {
                $consult = Consultation::create([
                    'medecin_id' => $medecin->id,
                    'rendez_vous_id' => $rdv->id,
                    'titre' => $rdv->motif, // On reprend le motif comme titre
                    'diagnostic' => $faker->randomElement($diagnostics) . ". " . $faker->sentence(),
                    'notes_privees' => $faker->boolean(30) ? 'Patient anxieux.' : null,
                    'created_at' => $carbonDate, // Important pour l'historique
                    'updated_at' => $carbonDate
                ]);

                // 80% de chance d'avoir une ordonnance
                if ($faker->boolean(80)) {
                    // Générer un contenu de médicament aléatoire
                    $medsList = "- " . $faker->randomElement($medicaments) . " (3 fois par jour)\n- " . $faker->randomElement($medicaments) . " (Si douleur)";
                    
                    // Date de fin aléatoire (entre 5 et 30 jours après le RDV)
                    $dateFin = (clone $carbonDate)->addDays(rand(5, 30));

                    Ordonnance::create([
                        'consultation_id' => $consult->id,
                        'contenu' => $medsList,
                        'est_active' => 1,
                        'date_fin' => $dateFin,
                        'created_at' => $carbonDate,
                        'updated_at' => $carbonDate
                    ]);
                }
            }
        }

        echo "✅ 300 Rendez-vous & Consultations générés.\n";
        echo "--- Terminé ! Connectez-vous et testez la pagination. ---\n";
    }
}