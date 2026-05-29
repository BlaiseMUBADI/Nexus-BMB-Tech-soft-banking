<?php

namespace App\Models\Credit;

use Illuminate\Database\Eloquent\Model;
use App\Models\Clients\Compte;
use App\Models\RH\Agent;

class CreditDeblocage extends Model
{
    protected $table = 'tb_credit_deblocages';

    protected $fillable = [
        'credit_demande_id',
        'agent_matricule',
        'compte_debit_id',
        'guichet_solde_id',
        'compte_credit_id',
        'montant_debloque',
        'montant_caution',
        'devise',
        'frais_dossier',
        'montant_net_verse',
        'reference_transaction',
        'numero_ordre',
        'observations',
        'debloque_le',
    ];

    protected $casts = [
        'montant_debloque'   => 'decimal:2',
        'montant_caution'    => 'decimal:2',
        'frais_dossier'      => 'decimal:2',
        'montant_net_verse'  => 'decimal:2',
        'debloque_le'        => 'datetime',
    ];

    public function demande()
    {
        return $this->belongsTo(CreditDemande::class, 'credit_demande_id');
    }

    public function guichetSolde()
    {
        return $this->belongsTo(\App\Models\Caisse\CaissesGuichetSolde::class, 'guichet_solde_id');
    }

    public function compteCredit()
    {
        return $this->belongsTo(Compte::class, 'compte_credit_id', 'code_compte');
    }

    public function operateur()
    {
        return $this->belongsTo(Agent::class, 'agent_matricule', 'matricule');
    }

    // Accessors de compatibilite
    public function getDateDeblocageAttribute()
    {
        return $this->debloque_le;
    }

    public function getDatePremierRemboursementAttribute()
    {
        return $this->demande?->echeancier?->date_premier_remboursement;
    }

    public function getReferenceComptableAttribute(): ?string
    {
        return $this->reference_transaction;
    }
}
