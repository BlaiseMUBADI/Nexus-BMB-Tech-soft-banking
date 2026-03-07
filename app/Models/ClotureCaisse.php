<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle ClotureCaisse — Arrêté de caisse guichet
 * ------------------------------------------------
 * Enregistre l'arrêté de caisse effectué en fin de journée ou de shift.
 * Une ligne = clôture d'UN guichet dans UNE devise.
 *
 * Workflow :
 *   1. L'agent saisit son billetage physique (coupures × quantités)
 *   2. Le système compare solde_physique vs solde_comptable
 *   3. Si écart ≠ 0 → motif_ecart obligatoire
 *   4. Enregistrement + passage du guichet en statut FERME
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
        'solde_comptable',         // Solde système (db)
        'solde_physique',          // Solde compté physiquement
        'ecart_caisse',            // solde_physique - solde_comptable
        'detail_billetage',        // JSON { "50000": 3, "10000": 5, ... }
        'motif_ecart',             // Justification si écart ≠ 0
        'statut_ecart',            // EQUILIBRE | EXCEDENT | DEFICIT
        'agent_cloturant',         // Matricule de l'agent
        // ── Validation superviseur ─────────────────────────
        'statut_validation',       // EN_ATTENTE | VALIDE | REJETE
        'validateur_matricule',    // Matricule du superviseur
        'date_validation',         // Timestamp validation
        'observations_superviseur',// Commentaire superviseur
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
