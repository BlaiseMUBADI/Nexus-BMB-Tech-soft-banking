<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Agent extends Model
{
    protected $table = 'tb_agents';
    protected $primaryKey = 'matricule';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'matricule',
        'nom',
        'postnom',
        'prenom',
        'sexe',
        'date_naissance',
        'telephone',
        'email',
        'adresse',
        'photo',
        'date_embauche',
        'statut',
    ];
}
