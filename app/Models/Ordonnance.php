<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ordonnance extends Model
{
    use HasFactory;

    protected $table = 'ordonnances';

    protected $fillable = [
        'consultation_id', 
        'contenu', 
        'date_fin', 
        'est_active'
    ];

    protected $casts = [
        'date_fin' => 'date',
        'est_active' => 'boolean',
    ];


    public function consultation()
    {
        return $this->belongsTo(Consultation::class, 'consultation_id');
    }
}