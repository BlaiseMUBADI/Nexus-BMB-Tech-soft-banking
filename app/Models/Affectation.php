<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Affectation extends Model
{
    protected $table = 'tb_affectations';
    protected $fillable = [
        'agent_matricule',
        'poste_id',
        'date_debut',
        'date_fin',
    ];

    public function agent()
    {
        return $this->belongsTo(Agent::class, 'agent_matricule', 'matricule');
    }

    public function poste()
    {
        return $this->belongsTo(Poste::class, 'poste_id');
    }
}
