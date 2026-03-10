<?php

namespace App\Models\Caisse;

use Illuminate\Database\Eloquent\Model;
use App\Models\RH\Affectation;

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
     */
    public function soldes()
    {
        return $this->hasMany(CaissesGuichetSolde::class, 'guichet_id', 'id')
                    ->with('devise');
    }

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
