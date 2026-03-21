<?php

namespace App\Models\RH;

use Illuminate\Database\Eloquent\Model;
use App\Models\Caisse\CaissesGuichet;

/**
 * Affectation
 * -----------
 * Lie un agent à un poste RH ET/OU à un guichet de caisse.
 *   - poste_id   : poste dans l'organigramme (toujours renseigné)
 *   - guichet_id : guichet de caisse titulaire (nullable — absent hors caisse)
 */
class Affectation extends Model
{
    protected $table = 'tb_affectations';

    protected $fillable = [
        'agent_matricule',
        'poste_id',
        'guichet_id',
        'date_debut',
        'date_fin',
        'Etat',
    ];

    protected $casts = [
        'date_debut' => 'date',
        'date_fin'   => 'date',
    ];

    // ── Relations ────────────────────────────────────────────────

    public function agent()
    {
        return $this->belongsTo(Agent::class, 'agent_matricule', 'matricule');
    }

    public function poste()
    {
        return $this->belongsTo(Poste::class, 'poste_id');
    }

    /**
     * Guichet de caisse auquel cet agent est affecté (si renseigné).
     */
    public function guichet()
    {
        return $this->belongsTo(CaissesGuichet::class, 'guichet_id', 'id');
    }
}
