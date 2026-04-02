<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable; 
use Illuminate\Notifications\Notifiable;

class Medecin extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'medecins'; 

    protected $fillable = [
        'nom', 
        'prenom', 
        'email', 
        'password', 
        'specialite', 
        'telephone_pro'
    ];

    protected $hidden = [
        'password', 
        'remember_token',
    ];


    public function rendezVous()
    {
        return $this->hasMany(RendezVous::class, 'medecin_id');
    }


    public function consultations()
    {
        return $this->hasMany(Consultation::class, 'medecin_id');
    }
}