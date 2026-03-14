<?php

namespace App\Models\Caisse;

use Illuminate\Database\Eloquent\Model;
use App\Models\Clients\Compte;
use App\Models\Comptabilite\JournalComptable;
use App\Models\Tresorerie\Devise;

/**
 * Transaction
 * -----------
 * Table : tb_transactions
 *
 * Table unifiée des opérations caisse guichet :
 *
 *  DEPOT        — le guichet reçoit des espèces du client (solde ↑)
 *  RETRAIT      — le guichet remet des espèces au client (solde ↓)
 *  VIREMENT     — transfert comptable entre comptes (pas de mouvement physique)
 *  REMBOURSEMENT— le guichet rembourse le client (solde ↓)
 *  CHANGE       — échange de devises (devise_code ↑, devise_dest ↓)
 *  PAIEMENT     — le client paie un service en espèces (solde ↑)
 */
class Transaction extends Model
{
    protected $table      = 'tb_transactions';
    protected $primaryKey = 'id';
    public    $timestamps = true;

    // ── Constantes type ──────────────────────────────────────────
    const DEPOT          = 'DEPOT';
    const RETRAIT        = 'RETRAIT';
    const VIREMENT       = 'VIREMENT';
    const REMBOURSEMENT  = 'REMBOURSEMENT';
    const CHANGE         = 'CHANGE';
    const PAIEMENT       = 'PAIEMENT';

    // ── Constantes statut ────────────────────────────────────────
    const CONFIRME = 'CONFIRME';
    const ANNULE   = 'ANNULE';

    protected $fillable = [
        'compte_code',
        'agent_matricule',
        'guichet_id',
        'devise_code',
        'type',
        'montant',
        'montant_commission_total',
        'solde_compte_avant',
        'solde_compte_apres',
        'montant_total_client',
        'montant_net_client',
        'reference',
        'devise_dest',
        'montant_dest',
        'taux_change',
        'observations',
        'statut',
        'date_operation',
    ];

    protected $casts = [
        'montant'        => 'decimal:2',
        'montant_commission_total' => 'decimal:2',
        'solde_compte_avant' => 'decimal:2',
        'solde_compte_apres' => 'decimal:2',
        'montant_total_client' => 'decimal:2',
        'montant_net_client' => 'decimal:2',
        'montant_dest'   => 'decimal:2',
        'taux_change'    => 'decimal:6',
        'date_operation' => 'datetime',
    ];

    // ── Relations ────────────────────────────────────────────────

    /** Compte bancaire lié (nullable — null pour les ops espèces) */
    public function compte()
    {
        return $this->belongsTo(Compte::class, 'compte_code', 'code_compte');
    }

    /** Guichet émetteur */
    public function guichet()
    {
        return $this->belongsTo(CaissesGuichet::class, 'guichet_id', 'id');
    }

    /** Devise principale */
    public function devise()
    {
        return $this->belongsTo(Devise::class, 'devise_code', 'code_iso');
    }

    public function commissions()
    {
        return $this->hasMany(TransactionCommission::class, 'transaction_id', 'id');
    }

    public function journauxComptables()
    {
        return $this->hasMany(JournalComptable::class, 'transaction_id', 'id');
    }

    // ── Helpers statiques ────────────────────────────────────────

    /** Libellé lisible d'un type de transaction */
    public static function typeLabel(string $type): string
    {
        return match($type) {
            self::DEPOT         => 'Dépôt',
            self::RETRAIT       => 'Retrait',
            self::VIREMENT      => 'Virement',
            self::REMBOURSEMENT => 'Remboursement',
            self::CHANGE        => 'Change',
            self::PAIEMENT      => 'Paiement',
            default             => $type,
        };
    }

    /** Icône FontAwesome associée à un type de transaction */
    public static function typeIcon(string $type): string
    {
        return match($type) {
            self::DEPOT         => 'fa-arrow-down',
            self::RETRAIT       => 'fa-arrow-up',
            self::VIREMENT      => 'fa-exchange-alt',
            self::REMBOURSEMENT => 'fa-undo',
            self::CHANGE        => 'fa-sync-alt',
            self::PAIEMENT      => 'fa-money-bill-wave',
            default             => 'fa-circle',
        };
    }

    /** Classe CSS badge associée à un type de transaction */
    public static function typeBadgeClass(string $type): string
    {
        return match($type) {
            self::DEPOT         => 'badge-success',
            self::RETRAIT       => 'badge-danger',
            self::VIREMENT      => 'badge-info',
            self::REMBOURSEMENT => 'badge-warning',
            self::CHANGE        => 'badge-primary',
            self::PAIEMENT      => 'badge-secondary',
            default             => 'badge-light',
        };
    }

    /**
     * Types d'opérations autorisés selon le type de guichet.
     */
    public static function allowedTypesForGuichetType(?string $guichetType = null): array
    {
        $default = [
            self::DEPOT,
            self::RETRAIT,
            self::CHANGE,
            self::PAIEMENT,
            self::REMBOURSEMENT,
        ];

        if (strtoupper((string) $guichetType) === 'MOBILE') {
            return [
                self::DEPOT,
                self::CHANGE,
                self::PAIEMENT,
                self::REMBOURSEMENT,
            ];
        }

        return $default;
    }

    /**
     * Options prêtes à afficher dans les filtres / formulaires.
     */
    public static function operationTypeOptions(?string $guichetType = null): array
    {
        return collect(self::allowedTypesForGuichetType($guichetType))
            ->map(fn ($type) => [
                'value' => $type,
                'label' => self::typeLabel($type),
            ])
            ->values()
            ->all();
    }
}
