<?php

namespace App\Models\Tresorerie;

use App\Models\PortefeuilleAffectation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
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

    public function affectations()
    {
        return $this->hasMany(PortefeuilleAffectation::class, 'portefeuille_id', 'id');
    }

    public function affectationActive()
    {
        $today = now()->toDateString();

        return $this->hasOne(PortefeuilleAffectation::class, 'portefeuille_id', 'id')
            ->whereRaw('UPPER(Etat) = ?', ['ACTIF'])
            ->whereDate('date_debut', '<=', $today)
            ->where(function ($query) use ($today) {
                $query->whereNull('date_fin')
                    ->orWhereDate('date_fin', '>', $today);
            })
            ->latest('date_debut');
    }

    public function scopeAssignedToAgent(Builder $query, string $matricule): Builder
    {
        return $query->whereHas('affectationActive', function (Builder $q) use ($matricule): void {
            $q->where('agent_matricule', $matricule);
        });
    }
}
