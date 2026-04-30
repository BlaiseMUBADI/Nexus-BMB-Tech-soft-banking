<?php

namespace App\Models\Credit;

use Illuminate\Database\Eloquent\Model;
use App\Models\RH\Agent;

class CreditValidation extends Model
{
    protected $table = 'tb_credit_validations';

    protected $fillable = [
        'credit_demande_id',
        'type_validateur',
        'validateur_matricule',
        'decision',
        'montant_valide',
        'duree_mois_validee',
        'observations',
        'conditions',
        'ordre_etape',
        'etape_precedente_ok',
        'valide_le',
        'signature_agent',
        'nom_signataire',
        'ip_validation',
    ];

    protected $casts = [
        'montant_valide'       => 'decimal:2',
        'duree_mois_validee'   => 'integer',
        'etape_precedente_ok'  => 'boolean',
        'valide_le'            => 'datetime',
    ];

    // Labels lisibles
    public static array $labels = [
        'AGENT_CREDIT'      => 'Chargé de crédit',
        'CHARGE_OPERATIONS' => 'Chargé des opérations',
        'CONTROLEUR'        => 'Contrôleur interne',
        'GERANT'            => 'Gérant / Directeur',
    ];

    public function label(): string
    {
        return self::$labels[$this->type_validateur] ?? $this->type_validateur;
    }

    public function badgeDecision(): string
    {
        $map = [
            'EN_ATTENTE'            => ['secondary', 'En attente'],
            'APPROUVE'              => ['success',   'Approuvé'],
            'APPROUVE_AVEC_RESERVE' => ['warning',   'Approuvé avec réserve'],
            'REJETE'                => ['danger',    'Rejeté'],
        ];
        [$color, $text] = $map[$this->decision] ?? ['secondary', $this->decision];
        return "<span class=\"badge badge-{$color}\">{$text}</span>";
    }

    public function demande()
    {
        return $this->belongsTo(CreditDemande::class, 'credit_demande_id');
    }

    public function validateur()
    {
        return $this->belongsTo(Agent::class, 'validateur_matricule', 'matricule');
    }

    // Accessors de compatibilite pour vues
    public function getDateValidationAttribute()
    {
        return $this->valide_le;
    }

    public function getCommentaireAttribute(): ?string
    {
        return $this->observations;
    }

    public function getMontantProposeAttribute(): ?string
    {
        return $this->montant_valide;
    }
}
