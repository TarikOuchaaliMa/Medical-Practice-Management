<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Medecin;

class MedecinSeeder extends Seeder
{
    public function run()
    {

        if (Medecin::where('email', 'Dr.ElHalimi.AlWarda@gmail.com')->doesntExist()) {
            
            Medecin::create([
                'nom' => 'El Halimi',
                'prenom' => 'Al Warda',
                'email' => 'Dr.ElHalimi.AlWarda@gmail.com',
                'password' => Hash::make('password123'), 
                'specialite' => 'Généraliste',
                'telephone_pro' => '0522123456'
            ]);

            $this->command->info('Médecin Dr. Dubois créé avec succès !');
        } else {
            $this->command->warn('Ce médecin existe déjà.');
        }
    }
}