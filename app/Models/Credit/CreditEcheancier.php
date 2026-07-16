<?php

namespace App\Models\Credit;

use Illuminate\Database\Eloquent\Model;

class CreditEcheancier extends Model
{
    protected $table = 'tb_credit_echeanciers';

    protected $fillable = [
        'credit_demande_id',
        'montant_capital',
        'taux_mensuel',
        'duree_mois',
        'date_premier_remboursement',
        'type_amortissement',
        'total_capital',
        'total_interets',
        'total_commission',
        'total_general',
    ];

    protected $casts = [
        'montant_capital'             => 'decimal:2',
        'taux_mensuel'                => 'decimal:4',
        'total_capital'               => 'decimal:2',
        'total_interets'              => 'decimal:2',
        'total_commission'            => 'decimal:2',
        'total_general'               => 'decimal:2',
        'date_premier_remboursement'  => 'date',
    ];

    public function demande()
    {
        return $this->belongsTo(CreditDemande::class, 'credit_demande_id');
    }

    public function echeances()
    {
        return $this->hasMany(CreditEcheance::class, 'echeancier_id')->orderBy('numero_echeance');
    }

    public function getMontantTotalAttribute(): ?string
    {
        return $this->total_general;
    }
}
