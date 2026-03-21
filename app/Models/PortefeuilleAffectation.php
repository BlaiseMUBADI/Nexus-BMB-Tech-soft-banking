<?php

namespace App\Models;

use App\Models\RH\Agent;
use App\Models\Tresorerie\Portefeuille;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class PortefeuilleAffectation extends Model
{
    protected $table = 'tb_affectations_portefeuilles';

    protected $fillable = [
        'portefeuille_id',
        'agent_matricule',
        'date_debut',
        'date_fin',
        'Etat',
        'motif',
        'effectue_par_user_id',
    ];

    protected $casts = [
        'date_debut' => 'date',
        'date_fin' => 'date',
    ];

    public function portefeuille()
    {
        return $this->belongsTo(Portefeuille::class, 'portefeuille_id', 'id');
    }

    public function agent()
    {
        return $this->belongsTo(Agent::class, 'agent_matricule', 'matricule');
    }

    public function scopeActives(Builder $query): Builder
    {
        $today = now()->toDateString();

        return $query
            ->whereRaw('UPPER(Etat) = ?', ['ACTIF'])
            ->whereDate('date_debut', '<=', $today)
            ->where(function (Builder $subQuery) use ($today): void {
                $subQuery->whereNull('date_fin')
                    ->orWhereDate('date_fin', '>', $today);
            });
    }
}
