<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Consultation extends Model
{
    use HasFactory;

    protected $table = 'consultations';

    protected $fillable = [
        'rendez_vous_id', 
        'medecin_id', 
        'titre', 
        'diagnostic', 
        'traitement', 
        'notes_privees'
    ];

    public function rendezVous()
    {
        return $this->belongsTo(RendezVous::class, 'rendez_vous_id');
    }

    public function medecin()
    {
        return $this->belongsTo(Medecin::class, 'medecin_id');
    }

    public function ordonnance()
    {
        return $this->hasOne(Ordonnance::class, 'consultation_id');
    }
}