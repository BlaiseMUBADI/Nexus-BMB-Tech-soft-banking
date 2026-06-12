<?php

namespace App\Models\Credit;

use Illuminate\Database\Eloquent\Model;
use App\Models\Clients\Compte;
use App\Models\RH\Agent;

class CreditRemboursement extends Model
{
    protected $table = 'tb_credit_remboursements';

    protected $fillable = [
        'credit_demande_id',
        'echeance_id',
        'agent_matricule',
        'compte_id',
        'montant_recu',
        'dont_capital',
        'dont_interet',
        'dont_penalite',
        'devise',
        'type_remboursement',
        'reference_caisse',
        'observations',
        'recu_le',
        'transaction_id', // Lien vers la transaction de caisse
    ];

    protected $casts = [
        'montant_recu'   => 'decimal:2',
        'dont_capital'   => 'decimal:2',
        'dont_interet'   => 'decimal:2',
        'dont_penalite'  => 'decimal:2',
        'recu_le'        => 'datetime',
    ];

    public function demande()
    {
        return $this->belongsTo(CreditDemande::class, 'credit_demande_id');
    }

    public function echeance()
    {
        return $this->belongsTo(CreditEcheance::class, 'echeance_id');
    }

    public function compte()
    {
        return $this->belongsTo(Compte::class, 'compte_id', 'code_compte');
    }

    public function caissier()
    {
        return $this->belongsTo(Agent::class, 'agent_matricule', 'matricule');
    }

    // Accessors de compatibilite
    public function getDatePaiementAttribute()
    {
        return $this->recu_le;
    }

    public function getMontantCapitalPayeAttribute(): ?string
    {
        return $this->dont_capital;
    }

    public function getMontantInteretPayeAttribute(): ?string
    {
        return $this->dont_interet;
    }

    public function getModePaiementAttribute(): ?string
    {
        return $this->type_remboursement;
    }

    public function getReferencePaiementAttribute(): ?string
    {
        return $this->reference_caisse;
    }
}
