<?php

namespace App\Models\Caisse;

use Illuminate\Database\Eloquent\Model;
use App\Models\Clients\Compte;
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
}
