<?php

namespace App\Models\Comptabilite;

use Illuminate\Database\Eloquent\Model;

class CategorieDepense extends Model
{
    protected $table = 'tb_categories_depenses';

    protected $fillable = [
        'libelle',
        'numero_compte_charge',
        'est_actif',
    ];

    protected $casts = [
        'est_actif' => 'boolean',
    ];

    public function compteCharge()
    {
        return $this->belongsTo(PlanComptable::class, 'numero_compte_charge', 'numero_compte');
    }

    public function depenses()
    {
        return $this->hasMany(\App\Models\Caisse\Depense::class, 'categorie_id');
    }

    /**
     * Comptes éligibles pour une catégorie de dépense :
     * uniquement des comptes de charge (classe 6) mouvementables.
     */
    public static function comptesEligibles()
    {
        return PlanComptable::where('classe_ohada', '6')
            ->where('type_compte', 'CHARGE')
            ->where('est_mouvementable', true)
            ->where('est_actif', true)
            ->orderBy('numero_compte')
            ->get();
    }
}
