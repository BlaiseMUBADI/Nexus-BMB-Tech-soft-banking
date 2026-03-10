<?php

namespace App\Models\Caisse;

use Illuminate\Database\Eloquent\Model;
use App\Models\RH\Agent;

/**
 * MouvementInterCaisse
 * ---------------------
 * Table : tb_mouvements_inter_caisses
 *
 * Enregistre tous les flux de fonds entre guichets / coffre :
 *   ALIMENTATION  → coffre (source=null) vers un guichet
 *   DEGAGEMENT    → guichet vers coffre (dest=null)
 *   TRANSFERT     → guichet vers guichet
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
