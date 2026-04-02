<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Medecin;
use Illuminate\Support\Facades\Hash;

class admin extends Seeder
{
    public function run(): void
    {
        Medecin::create([
            'nom'            => 'Admin1',
            'prenom'         => 'Admin1',
            'email'          => 'admin@example.com',
            'password'       => Hash::make('admin12345'),
            'specialite'     => 'Super Admin',
            'telephone_pro'  => '0600000000',
        ]);
    }
}
