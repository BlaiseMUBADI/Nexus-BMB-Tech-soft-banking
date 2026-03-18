<?php

namespace App\Models\Credit;

use Illuminate\Database\Eloquent\Model;
use App\Models\RH\Agent;

class CreditAudit extends Model
{
    protected $table = 'tb_credit_audits';

    protected $fillable = [
        'credit_demande_id',
        'acteur_matricule',
        'type_action',
        'ancien_statut',
        'nouveau_statut',
        'details',
        'ip_address',
    ];

    // Labels lisibles
    public static array $labels = [
        'CREATION'              => 'Création du dossier',
        'SOUMISSION'            => 'Soumission pour analyse',
        'ANALYSE_DEMARREE'      => 'Analyse démarrée',
        'ANALYSE_COMPLETE'      => 'Analyse complétée',
        'VALIDATION_PARTIELLE'  => 'Validation partielle',
        'VALIDATION_COMPLETE'   => 'Validation complète',
        'REJET'                 => 'Rejet du dossier',
        'DEBLOCAGE'             => 'Déblocage des fonds',
        'REMBOURSEMENT'         => 'Remboursement enregistré',
        'ANNULATION'            => 'Annulation du dossier',
        'SUSPENSION'            => 'Dossier suspendu',
        'LEVER_SUSPENSION'      => 'Suspension levée',
        'SIGNALEMENT_SUSPECT'   => 'Signalement suspect',
        'LEVER_SUSPICION'       => 'Suspicion levée',
        'MODIFICATION'          => 'Modification du dossier',
    ];

    public function labelAction(): string
    {
        return self::$labels[$this->type_action] ?? $this->type_action;
    }

    public function demande()
    {
        return $this->belongsTo(CreditDemande::class, 'credit_demande_id');
    }

    public function utilisateur()
    {
        return $this->belongsTo(Agent::class, 'acteur_matricule', 'matricule');
    }

    public function getCommentaireAttribute(): ?string
    {
        return $this->details;
    }
}
