<?php

namespace App\Models\Tresorerie;

use Illuminate\Database\Eloquent\Model;
use App\Models\RH\Agent;

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
        return $this->belongsTo(Agent::class, 'agent_matricule', 'matricule');
    }
}
