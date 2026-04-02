<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RendezVous extends Model
{
    use HasFactory;

    protected $table = 'rendez_vous'; 

    protected $fillable = [
        'user_id', 
        'medecin_id', 
        'date_heure', 
        'motif', 
        'statut'
    ];

    protected $casts = [
        'date_heure' => 'datetime', 
    ];


    public function patient()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function medecin()
    {
        return $this->belongsTo(Medecin::class, 'medecin_id');
    }

    public function consultation()
    {
        return $this->hasOne(Consultation::class, 'rendez_vous_id');
    }
}
