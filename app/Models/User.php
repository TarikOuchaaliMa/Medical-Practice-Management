<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable; 
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    protected $table = 'users';

    protected $fillable = [
        'nom', 
        'prenom', 
        'email', 
        'password', 
        'telephone', 
        'adresse', 
        'date_naissance', 
        'groupe_sanguin', 
        'allergies_connues', 
        'statut_medical', 
        'derniere_visite'
    ];

    protected $hidden = [
        'password', 
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'date_naissance' => 'date',
        'derniere_visite' => 'date',
    ];


    public function rendezVous()
    {
        return $this->hasMany(RendezVous::class, 'user_id');
    }


    public function consultations()
    {
        return $this->hasManyThrough(Consultation::class, RendezVous::class, 'user_id', 'rendez_vous_id');
    }
}