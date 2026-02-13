<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $fillable = [
        'nom',
        'postnom',
        'prenom',
        'sexe',
        'date_naissance',
        'lieu_naissance',
        'adresse',
        'etat_civil',
        'nom_conjoint',
        'zone',
        'type_piece_identite',
        'lieu_delivrance_piece',
        'date_delivrance_piece',
        'numero_piece_identite',
        'photo',
        // Partie 6 : Activité économique
        'secteur_activite',
        'type_activite',
        'nom_entreprise',
        'adresse_entreprise',
        'telephone_entreprise',
        'statut_entreprise',
        'nombre_annees_experience',
        'revenu_mensuel',
        'autres_details_activite',
    ];
}
