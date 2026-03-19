<?php

namespace App\Models\Clients;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use App\Models\Zone;

class Client extends Model
{
    protected $table = 'tb_clients';
    protected $primaryKey = 'matricule';
    public $incrementing = false;
    protected $keyType = 'string';

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

    public function zone()
    {
        return $this->belongsTo(Zone::class, 'code_zone', 'code_zone');
    }

    public function comptes()
    {
        return $this->hasMany(Compte::class, 'client_matricule', 'matricule');
    }

    public function getFullNameAttribute(): string
    {
        return trim(preg_replace('/\s+/', ' ', implode(' ', array_filter([
            $this->nom,
            $this->postnom,
            $this->prenom,
        ], fn ($value) => filled($value)))));
    }

    public function scopeSearchFullName(Builder $query, ?string $search): Builder
    {
        $search = trim(preg_replace('/\s+/', ' ', (string) $search));

        if ($search === '') {
            return $query;
        }

        return $query->whereRaw(
            "LOWER(CONCAT_WS(' ', NULLIF(TRIM(nom), ''), NULLIF(TRIM(postnom), ''), NULLIF(TRIM(prenom), ''))) LIKE LOWER(?)",
            ['%' . $search . '%']
        );
    }
}
