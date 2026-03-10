<?php

namespace App\Models\Caisse;

use Illuminate\Database\Eloquent\Model;
use App\Models\RH\Agent;

/**
 * DemandeModification
 * --------------------
 * Table : tb_demandes_modification
 *
 * Demande faite par un guichetier pour modifier ou supprimer une
 * opération existante. La demande doit être approuvée par un superviseur.
 */
class DemandeModification extends Model
{
    protected $table      = 'tb_demandes_modification';
    protected $primaryKey = 'id';
    public    $timestamps = true;

    const EN_ATTENTE = 'EN_ATTENTE';
    const APPROUVEE  = 'APPROUVEE';
    const REJETEE    = 'REJETEE';

    const MODIFICATION = 'MODIFICATION';
    const SUPPRESSION  = 'SUPPRESSION';

    protected $fillable = [
        'transaction_id',
        'reference_operation',
        'guichet_id',
        'compte_code',
        'client_nom',
        'type_operation',
        'devise_code',
        'ancien_montant',
        'anciennes_observations',
        'type_demande',
        'agent_matricule',
        'motif',
        'nouveau_montant',
        'nouvelles_observations',
        'statut',
        'superviseur_matricule',
        'commentaire_superviseur',
        'traitee_le',
    ];

    protected $casts = [
        'ancien_montant'  => 'decimal:2',
        'nouveau_montant' => 'decimal:2',
        'traitee_le'      => 'datetime',
    ];

    // ── Relations ────────────────────────────────────────────────

    public function transaction()
    {
        return $this->belongsTo(Transaction::class, 'transaction_id');
    }

    public function guichet()
    {
        return $this->belongsTo(CaissesGuichet::class, 'guichet_id', 'id');
    }

    public function agentDemandeur()
    {
        return $this->belongsTo(Agent::class, 'agent_matricule', 'matricule');
    }

    public function superviseur()
    {
        return $this->belongsTo(Agent::class, 'superviseur_matricule', 'matricule');
    }
}
