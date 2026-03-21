<?php

namespace App\Models;

use App\Models\RH\Agent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ZoneAffectation extends Model
{
    protected $table = 'tb_affectations_zones';

    protected $fillable = [
        'code_zone',
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

    public function zone()
    {
        return $this->belongsTo(Zone::class, 'code_zone', 'code_zone');
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
