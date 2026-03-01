<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $table = 'tb_clients';
    protected $primaryKey = 'matricule';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'matricule',
        'nom',
        'postnom',
        'prenom',
        'email',
        'telephone',
        'sexe',
        'date_naissance',
        'lieu_naissance',
        'adresse',
        'etat_civil',
        'nom_conjoint',
        'code_zone',
        'type_piece_identite',
        'lieu_delivrance_piece',
        'date_delivrance_piece',
        'numero_piece_identite',
        'photo',
        'secteur_activite',
        'type_activite',
        'nom_entreprise',
        'adresse_entreprise',
        'telephone_entreprise',
        'statut_entreprise',
        'nombre_annees_experience',
        'revenu_mensuel',
        'revenu_mensuel_devise',
        'autres_details_activite',
        'created_at',
        'updated_at',
    ];

    public static function boot()
    {
        parent::boot();
        static::creating(function ($client) {
            if (!$client->matricule) {
                $annee = date('y');
                $prefix = 'CL-EBENKGA-' . $annee . '-';
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
                $client->matricule = $prefix . str_pad($next, 5, '0', STR_PAD_LEFT);
            }
        });
    }
    /**
     * Relation avec la zone
     */
    public function zone()
    {
        return $this->belongsTo(Zone::class, 'code_zone', 'code_zone');
    }
}
