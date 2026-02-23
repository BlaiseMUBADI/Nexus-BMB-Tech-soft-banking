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

    public static function boot()
    {
        parent::boot();
        static::creating(function ($agent) {
            if (!$agent->matricule) {
                $codes = self::pluck('matricule')->map(function ($c) {
                    return (int)preg_replace('/[^0-9]/', '', $c);
                })->filter()->sort()->values();
                $next = 1;
                foreach ($codes as $num) {
                    if ($num != $next) break;
                    $next++;
                }
                $agent->matricule = 'EBEN-AG' . $next;
            }
        });
    }

    // Relation vers le poste de l'agent
    public function poste()
    {
        return $this->belongsTo(Poste::class, 'poste_id');
    }
}
