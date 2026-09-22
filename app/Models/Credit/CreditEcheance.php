<?php

namespace App\Models\Credit;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class CreditEcheance extends Model
{
    protected $table = 'tb_credit_echeances';

    protected $fillable = [
        'echeancier_id',
        'numero_echeance',
        'date_echeance',
        'capital_restant_debut',
        'capital_echeance',
        'interet_echeance',
        'commission_echeance',
        'total_echeance',
        'capital_restant_fin',
        'statut',
        'montant_paye',
        'date_paiement_effectif',
    ];

    protected $casts = [
        'capital_restant_debut'  => 'decimal:2',
        'capital_echeance'       => 'decimal:2',
        'interet_echeance'       => 'decimal:2',
        'commission_echeance'    => 'decimal:2',
        'total_echeance'         => 'decimal:2',
        'capital_restant_fin'    => 'decimal:2',
        'montant_paye'           => 'decimal:2',
        'date_echeance'          => 'date',
        'date_paiement_effectif' => 'date',
    ];

    public function echeancier()
    {
        return $this->belongsTo(CreditEcheancier::class, 'echeancier_id');
    }

    public function remboursements()
    {
        return $this->hasMany(CreditRemboursement::class, 'echeance_id');
    }

    public function isEnRetard(): bool
    {
        return $this->statut === 'EN_RETARD'
            || ($this->statut === 'EN_ATTENTE' && !empty($this->date_echeance) && (string) $this->date_echeance < now()->toDateString());
    }

    /**
     * Jours OUVRABLES de retard soldés par le règlement de cette échéance
     * (banque ouverte lundi-samedi, dimanche exclu — même règle que la
     * liste recouvrement) : échéance PAYE réglée après sa date d'échéance.
     * 0 si payée à temps, le jour même, ou non soldée.
     */
    public function getJoursRetardSoldeAttribute(): int
    {
        if (empty($this->date_echeance) || empty($this->date_paiement_effectif)) {
            return 0;
        }

        $due  = Carbon::parse($this->date_echeance)->startOfDay();
        $paye = Carbon::parse($this->date_paiement_effectif)->startOfDay();
        if ($paye->lessThanOrEqualTo($due)) {
            return 0;
        }

        $jours = 0;
        $d = $due->copy()->addDay();
        while ($d->lessThanOrEqualTo($paye) && $jours < 3650) {
            if ($d->dayOfWeekIso <= 6) {
                $jours++;
            }
            $d->addDay();
        }
        return $jours;
    }

    // Accessors de compatibilite
    public function getMontantCapitalAttribute(): ?string
    {
        return $this->capital_echeance;
    }

    public function getMontantInteretAttribute(): ?string
    {
        return $this->interet_echeance;
    }

    public function getMontantCommissionAttribute(): ?string
    {
        return $this->commission_echeance;
    }

    public function getMontantTotalAttribute(): ?string
    {
        return $this->total_echeance;
    }
}
