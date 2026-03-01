<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Portefeuille extends Model
{
    protected $table = 'tb_portefeuilles_agents';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'nom_portefeuille',
        'agent_matricule',
        'taux_commission_agent',
    ];

    // Relations
    public function agent()
    {
        return $this->belongsTo(\App\Models\Agent::class, 'agent_matricule', 'matricule');
    }
}
