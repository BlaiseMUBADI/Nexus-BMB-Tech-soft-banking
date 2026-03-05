<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle CaissesGuichetSolde
 * ---------------------------
 * Représente le solde d'UN guichet dans UNE devise.
 * Table : tb_caisses_guichets_soldes
 *
 * Un guichet peut avoir N soldes (un par devise gérée).
 * Contrainte UNIQUE : (guichet_id, devise_code)
 */
class CaissesGuichetSolde extends Model
{
    protected $table      = 'tb_caisses_guichets_soldes';
    protected $primaryKey = 'id';

    // Pas de created_at, seulement updated_at auto-géré par MySQL
    public $timestamps  = false;

    protected $fillable = [
        'guichet_id',
        'devise_code',
        'solde_en_caisse',
    ];

    protected $casts = [
        'solde_en_caisse' => 'decimal:2',
    ];

    // ── Relations ────────────────────────────────────────────────

    /** Guichet propriétaire de ce solde */
    public function guichet()
    {
        return $this->belongsTo(CaissesGuichet::class, 'guichet_id', 'id');
    }

    /** Devise de ce solde */
    public function devise()
    {
        return $this->belongsTo(Devise::class, 'devise_code', 'code_iso');
    }
}
