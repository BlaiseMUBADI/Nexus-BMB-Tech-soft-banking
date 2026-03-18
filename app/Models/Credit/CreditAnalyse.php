<?php

namespace App\Models\Credit;

use Illuminate\Database\Eloquent\Model;

class CreditAnalyse extends Model
{
    protected $table = 'tb_credit_analyses';

    protected $fillable = [
        'credit_demande_id',
        'analyseur_matricule',
        'revenu_mensuel_verifie',
        'capacite_remboursement',
        'ratio_endettement',
        'score_risque',
        'historique_credit',
        'garanties_evaluees',
        'observations',
        'recommandation',
        'montant_recommande',
        'statut',
        'complete_le',
    ];

    protected $casts = [
        'revenu_mensuel_verifie' => 'decimal:2',
        'capacite_remboursement' => 'decimal:2',
        'ratio_endettement'      => 'decimal:2',
        'montant_recommande'     => 'decimal:2',
        'complete_le'            => 'datetime',
    ];

    public function demande()
    {
        return $this->belongsTo(CreditDemande::class, 'credit_demande_id');
    }

    // Accessors de compatibilite pour les vues
    public function getTauxEndettementAttribute(): ?string
    {
        return $this->ratio_endettement;
    }

    public function getRevenuMensuelNetAttribute(): ?string
    {
        return $this->revenu_mensuel_verifie;
    }

    public function getActivitePrincipaleAttribute(): ?string
    {
        return $this->historique_credit;
    }

    public function getValeurGarantieAttribute(): ?string
    {
        return null;
    }

    public function getMontantProposeAttribute(): ?string
    {
        return $this->montant_recommande;
    }

    public function getStatutAnalyseAttribute(): ?string
    {
        return $this->statut;
    }

    public function getDateAnalyseAttribute()
    {
        return $this->complete_le;
    }
}
