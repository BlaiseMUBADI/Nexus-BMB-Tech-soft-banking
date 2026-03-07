<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle CaissesGuichet
 * ----------------------
 * Table mère des guichets (sans solde ni devise directs).
 * Table : tb_caisses_guichets
 *
 * Architecture multi-devises :
 *   - Les soldes sont dans tb_caisses_guichets_soldes (relation soldes)
 *   - L'agent titulaire est dans tb_affectations (relation affectationActive)
 */
class CaissesGuichet extends Model
{
    protected $table      = 'tb_caisses_guichets';
    protected $primaryKey = 'id';
    public $timestamps    = true;

    protected $fillable = [
        'code_guichet',
        'type_guichet',
        'intitule',
        'statut_operationnel',
    ];

    // ── Scopes utiles ────────────────────────────────────────────

    /** Guichets opérationnels (pas le coffre central) */
    public function scopeOperationnels($query)
    {
        return $query->where('type_guichet', '!=', 'CENTRAL');
    }

    /** Uniquement le coffre central */
    public function scopeCentral($query)
    {
        return $query->where('type_guichet', 'CENTRAL');
    }

    // ── Relations ────────────────────────────────────────────────

    /**
     * Soldes multi-devises du guichet.
     * Un solde par devise gérée (CDF, USD, EUR…)
     * Utilisation : $guichet->soldes (collection)
     */
    public function soldes()
    {
        return $this->hasMany(CaissesGuichetSolde::class, 'guichet_id', 'id')
                    ->with('devise');
    }

    /**
     * Affectation active de l'agent sur ce guichet.
     * Retourne la dernière affectation dont Etat = 'ACTIF'
     * pointant vers un poste lié à ce guichet_id.
     *
     * Note : la liaison agent ↔ guichet passe par le poste
     * dans tb_affectations. Si ton projet stocke directement
     * le guichet_id dans les affectations, adapte la FK ici.
     */
    public function affectations()
    {
        return $this->hasMany(Affectation::class, 'guichet_id', 'id');
    }

    /**
     * Affectation courante (Etat = ACTIF) avec l'agent.
     */
    public function affectationActive()
    {
        return $this->hasOne(Affectation::class, 'guichet_id', 'id')
                    ->where('Etat', 'ACTIF')
                    ->latest('date_debut')
                    ->with('agent');
    }
}
