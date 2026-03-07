<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * MouvementInterCaisse
 * ---------------------
 * Table : tb_mouvements_inter_caisses
 *
 * Enregistre tous les flux de fonds entre guichets / coffre :
 *   ALIMENTATION  → coffre (source=null) vers un guichet
 *   DEGAGEMENT    → guichet vers coffre (dest=null)
 *   TRANSFERT     → guichet vers guichet
 *
 * Cycle double-validation :
 *   EN_ATTENTE → VALIDE (superviseur) → CONFIRME (agent)
 *   ou ANNULE à n'importe quelle étape.
 *   Si le superviseur alimente directement depuis l'admin : CONFIRME d'emblée.
 */
class MouvementInterCaisse extends Model
{
    protected $table      = 'tb_mouvements_inter_caisses';
    public    $timestamps = false;

    protected $fillable = [
        'guichet_source_id',
        'guichet_dest_id',
        'agent_initiateur',
        'type_flux',
        'montant',
        'devise_code',
        'reference_bordereau',
        'date_mouvement',
        'statut',
        'validateur_matricule',
        'observations',
    ];

    protected $casts = [
        'montant'        => 'decimal:2',
        'date_mouvement' => 'datetime',
    ];

    // ── Relations ────────────────────────────────────────────────

    public function guichetSource()
    {
        return $this->belongsTo(CaissesGuichet::class, 'guichet_source_id', 'id');
    }

    public function guichetDest()
    {
        return $this->belongsTo(CaissesGuichet::class, 'guichet_dest_id', 'id');
    }

    public function agentInitiateur()
    {
        return $this->belongsTo(Agent::class, 'agent_initiateur', 'matricule');
    }

    public function validateur()
    {
        return $this->belongsTo(Agent::class, 'validateur_matricule', 'matricule');
    }
}
