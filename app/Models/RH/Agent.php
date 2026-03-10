<?php

namespace App\Models\RH;

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

    public static function boot()
    {
        parent::boot();
        static::creating(function ($agent) {
            if (!$agent->matricule) {
                $annee = date('y');
                $prefix = 'AG-EBENKGA-' . $annee . '-';
                $codes = self::where('matricule', 'like', $prefix.'%')
                    ->pluck('matricule')
                    ->map(function($c) use ($prefix) {
                        return (int)preg_replace('/[^0-9]/', '', str_replace($prefix, '', $c));
                    })->filter()->sort()->values();
                $next = 1;
                foreach ($codes as $num) {
                    if ($num != $next) break;
                    $next++;
                }
                $agent->matricule = $prefix . str_pad($next, 5, '0', STR_PAD_LEFT);
            }
        });
    }

    // Relation vers le poste de l'agent (champ direct sur tb_agents)
    public function poste()
    {
        return $this->belongsTo(Poste::class, 'poste_id');
    }

    // Toutes les affectations de l'agent
    public function affectations()
    {
        return $this->hasMany(Affectation::class, 'agent_matricule', 'matricule');
    }
}
