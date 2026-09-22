<?php

namespace App\Models\Credit;

use Illuminate\Database\Eloquent\Model;
use App\Models\Clients\Compte;
use App\Models\RH\Agent;
use Carbon\Carbon;

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
        'dont_commission',
        'dont_penalite',
        'devise',
        'type_remboursement',
        'reference_caisse',
        'observations',
        'recu_le',
        'transaction_id', // Lien vers la transaction de caisse
    ];

    protected $casts = [
        'montant_recu'    => 'decimal:2',
        'dont_capital'    => 'decimal:2',
        'dont_interet'    => 'decimal:2',
        'dont_commission' => 'decimal:2',
        'dont_penalite'   => 'decimal:2',
        'recu_le'         => 'datetime',
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

    /**
     * Jours OUVRABLES de retard soldés par ce paiement (banque ouverte
     * lundi-samedi, dimanche exclu — même règle que la liste recouvrement).
     * Échéance réglée après sa date → le client a payé « avec le retard » ;
     * 0 si le paiement est dans les temps ou sans échéance liée.
     */
    public function getJoursRetardSoldeAttribute(): int
    {
        $echeance = $this->echeance;
        if (!$echeance || empty($echeance->date_echeance) || !$this->recu_le) {
            return 0;
        }

        // Le badge « retard soldé » ne concerne que le paiement qui a SOLDÉ
        // l'échéance : une échéance encore PARTIELLEMENT_PAYE n'a pas son
        // retard soldé, et un versement partiel antérieur ne l'a pas soldée.
        if ($echeance->statut !== 'PAYE' || empty($echeance->date_paiement_effectif)) {
            return 0;
        }
        if (!Carbon::parse($this->recu_le)->startOfDay()
                ->isSameDay(Carbon::parse($echeance->date_paiement_effectif)->startOfDay())) {
            return 0;
        }

        $due      = Carbon::parse($echeance->date_echeance)->startOfDay();
        $paiement = Carbon::parse($this->recu_le)->startOfDay();
        if ($paiement->lessThanOrEqualTo($due)) {
            return 0;
        }

        // Retard = jours ouvrables dans (date_echeance, date_paiement]
        $jours = 0;
        $d = $due->copy()->addDay();
        while ($d->lessThanOrEqualTo($paiement) && $jours < 3650) {
            if ($d->dayOfWeekIso <= 6) {
                $jours++;
            }
            $d->addDay();
        }
        return $jours;
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

    public function getMontantCommissionPayeAttribute(): ?string
    {
        return $this->dont_commission;
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
