<?php

namespace App\Models\Credit;

use Illuminate\Database\Eloquent\Model;
use App\Models\Clients\Client;
use App\Models\Clients\Compte;
use App\Models\Tresorerie\Portefeuille;
use App\Models\Zone;

class CreditDemande extends Model
{
    protected $table = 'tb_credit_demandes';

    protected $fillable = [
        'numero_dossier',
        'client_matricule',
        'compte_id',
        'portefeuille_id',
        'code_zone',
        'agent_createur_matricule',
        'montant_demande',
        'devise',
        'duree_mois',
        'taux_interet_mensuel',
        'type_credit',
        'objet_credit',
        'garantie_description',
        'montant_approuve',
        'montant_total_echeances',
        'total_interets',
        'statut_global',
        'est_annule',
        'motif_annulation',
        'annule_par_matricule',
        'annule_le',
        'est_suspendu',
        'motif_suspension',
        'suspendu_par_matricule',
        'suspendu_le',
        'est_suspect',
        'motif_suspicion',
        'signale_par_matricule',
        'signale_le',
        'soumis_le',
    ];

    protected $casts = [
        'montant_demande'         => 'decimal:2',
        'montant_approuve'        => 'decimal:2',
        'montant_total_echeances' => 'decimal:2',
        'total_interets'          => 'decimal:2',
        'taux_interet_mensuel'    => 'decimal:4',
        'est_annule'              => 'boolean',
        'est_suspendu'            => 'boolean',
        'est_suspect'             => 'boolean',
        'annule_le'               => 'datetime',
        'suspendu_le'             => 'datetime',
        'signale_le'              => 'datetime',
        'soumis_le'               => 'datetime',
    ];

    // ------------------------------------------------------------
    // Auto-génération du numéro de dossier
    // ------------------------------------------------------------
    protected static function booted(): void
    {
        static::creating(function (self $demande) {
            if (empty($demande->numero_dossier)) {
                $annee  = date('Y');
                $prefix = 'CRD-EBEN-' . $annee . '-';
                $last   = self::where('numero_dossier', 'like', $prefix . '%')
                    ->orderByDesc('id')
                    ->value('numero_dossier');
                $next = $last
                    ? (int) substr($last, strlen($prefix)) + 1
                    : 1;
                $demande->numero_dossier = $prefix . str_pad($next, 5, '0', STR_PAD_LEFT);
            }
        });
    }

    // ------------------------------------------------------------
    // Helpers de statut
    // ------------------------------------------------------------
    public function isBloque(): bool
    {
        return $this->est_annule || $this->est_suspendu || $this->est_suspect;
    }

    public function peutEtreDebloque(): bool
    {
        if ($this->isBloque()) {
            return false;
        }
        if ($this->statut_global !== 'PRET_A_DEBLOQUER') {
            return false;
        }
        // Toutes les validations doivent être APPROUVE ou APPROUVE_AVEC_RESERVE
        $validations = $this->validations;
        if ($validations->count() < 4) {
            return false;
        }
        foreach ($validations as $v) {
            if (!in_array($v->decision, ['APPROUVE', 'APPROUVE_AVEC_RESERVE'])) {
                return false;
            }
        }
        return true;
    }

    /*
     * Retourne un badge HTML Bootstrap selon le statut
     */
    public function badgeStatut(): string
    {
        $map = [
            'BROUILLON'        => ['secondary', 'Brouillon'],
            'SOUMIS'           => ['info',      'Soumis'],
            'EN_ANALYSE'       => ['primary',   'En analyse'],
            'EN_VALIDATION'    => ['warning',   'En validation'],
            'PRET_A_DEBLOQUER' => ['success',   'Prêt à débloquer'],
            'DEBLOQUE'         => ['success',   'Débloqué'],
            'EN_REMBOURSEMENT' => ['primary',   'En remboursement'],
            'EN_RETARD'        => ['danger',    'En retard'],
            'SOLDE'            => ['dark',      'Soldé'],
            'ANNULE'           => ['danger',    'Annulé'],
            'SUSPENDU'         => ['warning',   'Suspendu'],
            'SUSPECT'          => ['danger',    'Suspect'],
        ];
        [$color, $label] = $map[$this->statut_global] ?? ['secondary', $this->statut_global];
        return "<span class=\"badge badge-{$color}\">{$label}</span>";
    }

    // ------------------------------------------------------------
    // Relations
    // ------------------------------------------------------------
    public function client()
    {
        return $this->belongsTo(Client::class, 'client_matricule', 'matricule');
    }

    public function compte()
    {
        return $this->belongsTo(Compte::class, 'compte_id', 'code_compte');
    }

    public function portefeuille()
    {
        return $this->belongsTo(Portefeuille::class, 'portefeuille_id', 'id');
    }

    public function zone()
    {
        return $this->belongsTo(Zone::class, 'code_zone', 'code_zone');
    }

    public function analyse()
    {
        return $this->hasOne(CreditAnalyse::class, 'credit_demande_id');
    }

    public function validations()
    {
        return $this->hasMany(CreditValidation::class, 'credit_demande_id')->orderBy('ordre_etape');
    }

    public function pieces()
    {
        return $this->hasMany(CreditPiece::class, 'credit_demande_id');
    }

    public function deblocage()
    {
        return $this->hasOne(CreditDeblocage::class, 'credit_demande_id');
    }

    // Alias collection relation used by views/PDFs
    public function deblocages()
    {
        return $this->hasMany(CreditDeblocage::class, 'credit_demande_id');
    }

    public function echeancier()
    {
        return $this->hasOne(CreditEcheancier::class, 'credit_demande_id');
    }

    public function remboursements()
    {
        return $this->hasMany(CreditRemboursement::class, 'credit_demande_id')->orderByDesc('recu_le');
    }

    public function audits()
    {
        return $this->hasMany(CreditAudit::class, 'credit_demande_id')->orderByDesc('created_at');
    }

    // ------------------------------------------------------------
    // Accessors de compatibilite UI
    // ------------------------------------------------------------
    public function getStatutAttribute(): ?string
    {
        return $this->statut_global;
    }

    public function getMontantAccordeAttribute(): ?string
    {
        return $this->montant_approuve;
    }

    public function getDateSoumissionAttribute()
    {
        return $this->soumis_le;
    }

    public function getDateDeblocageAttribute()
    {
        return $this->deblocage?->debloque_le;
    }

    public function getFraisDossierAttribute(): ?string
    {
        return $this->deblocage?->frais_dossier;
    }

    public function getCapitalRestantAttribute(): ?string
    {
        $echeancier = $this->relationLoaded('echeancier') ? $this->echeancier : $this->echeancier()->with('echeances')->first();
        if (!$echeancier || !$echeancier->echeances || $echeancier->echeances->isEmpty()) {
            return null;
        }

        $prochaine = $echeancier->echeances
            ->whereIn('statut', ['EN_ATTENTE', 'EN_RETARD', 'PARTIELLEMENT_PAYE'])
            ->sortBy('numero_echeance')
            ->first();

        if ($prochaine) {
            return $prochaine->capital_restant_debut;
        }

        return '0.00';
    }
}
