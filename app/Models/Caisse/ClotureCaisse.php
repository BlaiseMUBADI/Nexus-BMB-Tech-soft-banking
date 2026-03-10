<?php

namespace App\Models\Caisse;

use Illuminate\Database\Eloquent\Model;
use App\Models\Tresorerie\Devise;

/**
 * Modèle ClotureCaisse — Arrêté de caisse guichet
 * ------------------------------------------------
 * Enregistre l'arrêté de caisse effectué en fin de journée ou de shift.
 * Une ligne = clôture d'UN guichet dans UNE devise.
 *
 * Table : tb_cloture_caisse
 */
class ClotureCaisse extends Model
{
    protected $table = 'tb_cloture_caisse';

    /** Utilise date_cloture comme created_at, pas d'updated_at */
    const CREATED_AT = 'date_cloture';
    const UPDATED_AT = null;

    protected $fillable = [
        'guichet_id',
        'devise_code',
        'solde_comptable',
        'solde_physique',
        'ecart_caisse',
        'detail_billetage',
        'motif_ecart',
        'statut_ecart',
        'agent_cloturant',
        'statut_validation',
        'validateur_matricule',
        'date_validation',
        'observations_superviseur',
    ];

    protected $casts = [
        'detail_billetage' => 'array',
        'solde_comptable'  => 'decimal:2',
        'solde_physique'   => 'decimal:2',
        'ecart_caisse'     => 'decimal:2',
        'date_cloture'     => 'datetime',
        'date_validation'  => 'datetime',
    ];

    // ── Constantes statut écart ──────────────────────────────
    const EQUILIBRE = 'EQUILIBRE';
    const EXCEDENT  = 'EXCEDENT';
    const DEFICIT   = 'DEFICIT';

    // ── Constantes statut validation superviseur ─────────────
    const VALIDATION_EN_ATTENTE = 'EN_ATTENTE';
    const VALIDATION_VALIDE     = 'VALIDE';
    const VALIDATION_REJETE     = 'REJETE';

    // ── Relations ────────────────────────────────────────────

    public function guichet()
    {
        return $this->belongsTo(CaissesGuichet::class, 'guichet_id', 'id');
    }

    public function devise()
    {
        return $this->belongsTo(Devise::class, 'devise_code', 'code_iso');
    }

    // ── Accessors ────────────────────────────────────────────

    /** Libellé humain du statut d'écart */
    public function getLibelleEcartAttribute(): string
    {
        return match ($this->statut_ecart) {
            self::EXCEDENT => 'Excédent',
            self::DEFICIT  => 'Déficit',
            default        => 'Équilibré',
        };
    }

    /** Classe CSS Bootstrap pour le badge statut */
    public function getBadgeEcartAttribute(): string
    {
        return match ($this->statut_ecart) {
            self::EXCEDENT => 'badge-warning text-dark',
            self::DEFICIT  => 'badge-danger',
            default        => 'badge-success',
        };
    }
}
