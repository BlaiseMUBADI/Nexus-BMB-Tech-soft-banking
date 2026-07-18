<?php

namespace App\Models\Comptabilite;

use Illuminate\Database\Eloquent\Model;

class CategorieRecette extends Model
{
    protected $table = 'tb_categories_recettes';

    protected $fillable = [
        'libelle',
        'numero_compte_produit',
        'est_actif',
    ];

    protected $casts = [
        'est_actif' => 'boolean',
    ];

    public function compteProduit()
    {
        return $this->belongsTo(PlanComptable::class, 'numero_compte_produit', 'numero_compte');
    }

    public function recettes()
    {
        return $this->hasMany(\App\Models\Caisse\Recette::class, 'categorie_id');
    }

    /**
     * Comptes éligibles pour une catégorie de recette :
     * uniquement des comptes de produit (classe 7) mouvementables.
     */
    public static function comptesEligibles()
    {
        return PlanComptable::where('classe_ohada', '7')
            ->where('type_compte', 'PRODUIT')
            ->where('est_mouvementable', true)
            ->where('est_actif', true)
            ->orderBy('numero_compte')
            ->get();
    }
}
