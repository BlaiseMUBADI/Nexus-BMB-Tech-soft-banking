<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $table = 'tb_clients';
    protected $primaryKey = 'matricule';
    public $incrementing = false;
    protected $keyType = 'string';

    // SUPPRIME LE BLOC $casts ICI s'il contient 'zone'
    // Aucun cast inutile ici, la relation zone() suffit

    protected $fillable = [
        'matricule', 'nom', 'postnom', 'prenom', 'email', 'telephone',
        'sexe', 'date_naissance', 'lieu_naissance', 'adresse', 'etat_civil',
        'nom_conjoint', 'code_zone', 'type_piece_identite', 'lieu_delivrance_piece',
        'date_delivrance_piece', 'numero_piece_identite', 'photo', 'secteur_activite',
        'type_activite', 'nom_entreprise', 'adresse_entreprise', 'telephone_entreprise',
        'statut_entreprise', 'nombre_annees_experience', 'revenu_mensuel',
        'revenu_mensuel_devise', 'others_details_activite',
    ];


    /**
     * Génération automatique du matricule lors de la création
     */
    protected static function booted()
    {
        parent::booted();
        static::creating(function ($client) {
            if (empty($client->matricule)) {
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

    public function comptes()
    {
        return $this->hasMany(\App\Models\Compte::class, 'client_matricule', 'matricule');
    }
}