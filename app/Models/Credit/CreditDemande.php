<?php

namespace App\Models\Credit;

use Illuminate\Database\Eloquent\Model;
use App\Models\Clients\Client;
use App\Models\Clients\Compte;
use App\Models\RH\Agent;
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
        'agent_analyse_matricule',
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
        'commission_totale',
        'pourcentage_caution',
        'pourcentage_frais_dossier',
        'pourcentage_frais_etude',
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
        'service_provenance',
        'referent_nom',
        'prelevement_auto_autorise',
    ];

    protected $casts = [
        'montant_demande'         => 'decimal:2',
        'montant_approuve'        => 'decimal:2',
        'montant_total_echeances' => 'decimal:2',
        'total_interets'          => 'decimal:2',
        'commission_totale'       => 'decimal:2',
        'pourcentage_caution'      => 'decimal:2',
        'pourcentage_frais_dossier'=> 'decimal:2',
        'pourcentage_frais_etude'  => 'decimal:2',
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
    //
    // IMPORTANT : on détermine le prochain numéro à partir du MAX() numérique
    // réel parmi tous les numero_dossier existants, PAS à partir du dernier
    // "id" inséré (orderByDesc('id')). Ces deux notions peuvent diverger :
    // certaines lignes historiques (import/seed) ont un id élevé mais un
    // numéro de dossier plus ancien/plus bas que d'autres lignes — dans ce
    // cas orderByDesc('id') renvoyait un numéro déjà attribué à un dossier
    // plus récent, provoquant une violation de contrainte unique à la
    // création (SQLSTATE 23000, doublon numero_dossier).
    //
    // On boucle en cas de collision résiduelle (concurrence de deux créations
    // simultanées) : au pire quelques tentatives, jamais un échec silencieux.
    protected static function booted(): void
    {
        static::creating(function (self $demande) {
            if (empty($demande->numero_dossier)) {
                $demande->numero_dossier = self::prochainNumeroDossier();
            }
        });
    }

    public static function prochainNumeroDossier(): string
    {
        $annee  = date('Y');
        $prefix = 'CRD-EBEN-' . $annee . '-';

        $max = (int) self::where('numero_dossier', 'like', $prefix . '%')
            ->selectRaw('MAX(CAST(SUBSTRING(numero_dossier, ?) AS UNSIGNED)) as max_num', [strlen($prefix) + 1])
            ->value('max_num');

        $next = $max + 1;

        // Défense supplémentaire : si ce numéro existe déjà malgré tout
        // (concurrence, données incohérentes), on avance jusqu'à en trouver
        // un libre plutôt que de laisser l'INSERT planter.
        while (self::where('numero_dossier', $prefix . str_pad($next, 5, '0', STR_PAD_LEFT))->exists()) {
            $next++;
        }

        return $prefix . str_pad($next, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Crée un dossier en garantissant un numero_dossier unique, même en cas
     * de double création SIMULTANÉE (deux requêtes calculent le même "prochain
     * numéro" avant qu'aucune n'ait validé son INSERT — prochainNumeroDossier()
     * seul ne protège pas contre ça, il n'y a pas de verrou). En cas de
     * collision, on relance avec un numéro fraîchement recalculé plutôt que de
     * laisser échouer la création (500) comme observé en production.
     *
     * Utiliser CETTE méthode (pas ::create() directement) partout où un
     * dossier est créé avec numero_dossier potentiellement vide/auto-généré.
     */
    public static function creerAvecNumeroUnique(array $attributes): self
    {
        // Si l'appelant a fourni un numéro EXPLICITE (ex: import d'un ancien
        // dossier avec son numéro historique d'origine), on ne le remplace
        // JAMAIS silencieusement par un numéro auto-généré en cas de collision
        // — ce serait une perte de traçabilité. Une seule tentative : l'erreur
        // doit remonter clairement pour que l'utilisateur choisisse un autre
        // numéro. La régénération automatique ne s'applique qu'aux numéros
        // laissés vides (auto-générés par booted()::creating()).
        $numeroImposeParAppelant = !empty($attributes['numero_dossier'] ?? null);
        $tentativesMax = $numeroImposeParAppelant ? 1 : 3;

        for ($tentative = 1; $tentative <= $tentativesMax; $tentative++) {
            try {
                return static::create($attributes);
            } catch (\Illuminate\Database\UniqueConstraintViolationException $e) {
                $estConflitNumero = str_contains($e->getMessage(), 'numero_dossier');
                if (!$estConflitNumero || $tentative === $tentativesMax) {
                    throw $e;
                }
                // Force la régénération d'un numéro FRAIS (pas celui qui vient
                // d'échouer) au prochain passage dans booted()::creating().
                unset($attributes['numero_dossier']);
            }
        }

        // Jamais atteint : la boucle retourne ou lève systématiquement.
        throw new \RuntimeException('creerAvecNumeroUnique: état inattendu.');
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
        
        $isExpected = in_array($this->statut_global, array_keys($map));
        $label = str_replace('_', ' ', $this->statut_global);
        $color = $isExpected ? $map[$this->statut_global][0] : 'dark';
        
        return "<span class=\"badge badge-{$color}\">{$label}</span>";
    }

    /**
     * Recalcule le statut du dossier (EN_RETARD / EN_REMBOURSEMENT / SOLDE) à
     * partir de l'état RÉEL des échéances — SOURCE UNIQUE de cette logique,
     * à appeler après tout événement pouvant changer la situation de retard
     * (remboursement manuel, recouvrement automatique, affichage du détail).
     *
     * Corrige le bug historique où `statut_global` restait bloqué sur
     * EN_RETARD après règlement de l'échéance en retard (aucun code ne
     * repassait le dossier à EN_REMBOURSEMENT quand il restait des échéances
     * futures non soldées) — d'où l'incohérence entre la liste des dossiers
     * (qui lit `statut_global`, périmé) et le détail du dossier.
     *
     * Ne touche PAS les dossiers hors circuit de remboursement actif
     * (BROUILLON, SOUMIS, ANNULE, SUSPENDU, etc.) : uniquement DEBLOQUE,
     * EN_REMBOURSEMENT et EN_RETARD.
     *
     * @return bool true si le statut (dossier ou une échéance) a changé.
     */
    public function refreshStatutRetard(): bool
    {
        if (!in_array($this->statut_global, ['DEBLOQUE', 'EN_REMBOURSEMENT', 'EN_RETARD'], true)) {
            return false;
        }

        $echeancier = $this->echeancier ?? $this->echeancier()->with('echeances')->first();
        if (!$echeancier) {
            return false;
        }

        $today = now()->toDateString();
        $echeances = $echeancier->echeances;
        $aChange = false;

        // 1. Toute échéance EN_ATTENTE (rien payé) dont la date est dépassée
        //    devient EN_RETARD. BUG corrigé : ne JAMAIS inclure ici les
        //    échéances PARTIELLEMENT_PAYE — un règlement partiel avait été
        //    enregistré correctement (montant_paye mis à jour) mais ce même
        //    code écrasait ensuite son statut en EN_RETARD à la prochaine
        //    vérification, effaçant visuellement l'information "paiement
        //    partiel reçu" (le montant restait juste, seul le badge de
        //    statut redevenait EN_RETARD). Une échéance PARTIELLEMENT_PAYE
        //    encore en retard fait déjà passer le DOSSIER en EN_RETARD via
        //    le calcul $aRetard ci-dessous, sans avoir besoin d'écraser le
        //    statut de l'échéance elle-même.
        foreach ($echeances as $echeance) {
            if ($echeance->statut !== 'EN_ATTENTE') {
                continue;
            }
            $dateEcheance = $echeance->date_echeance instanceof \Carbon\Carbon
                ? $echeance->date_echeance->toDateString()
                : (string) $echeance->date_echeance;
            if ($dateEcheance < $today) {
                $echeance->update(['statut' => 'EN_RETARD']);
                $aChange = true;
            }
        }

        // 2. Détermine l'état global à partir des échéances FRAÎCHEMENT relues
        //    (pas de la collection en mémoire, qui peut être partiellement stale
        //    après les update() ci-dessus sur d'autres instances du même jeu).
        $echeancesFraiches = $echeancier->echeances()->get();

        $toutesSoldees = $echeancesFraiches->every(fn ($e) => $e->statut === 'PAYE');
        $aRetard = $echeancesFraiches->contains(function ($e) use ($today) {
            if ($e->statut === 'PAYE') {
                return false;
            }
            $dateEcheance = $e->date_echeance instanceof \Carbon\Carbon
                ? $e->date_echeance->toDateString()
                : (string) $e->date_echeance;
            return $e->statut === 'EN_RETARD' || $dateEcheance < $today;
        });

        $statutActuel = $this->statut_global;
        $nouveauStatut = null;

        if ($toutesSoldees) {
            $nouveauStatut = 'SOLDE';
        } elseif ($aRetard && $statutActuel !== 'EN_RETARD') {
            $nouveauStatut = 'EN_RETARD';
        } elseif (!$aRetard && $statutActuel === 'EN_RETARD') {
            $nouveauStatut = 'EN_REMBOURSEMENT';
        } elseif (!$aRetard && $statutActuel === 'DEBLOQUE') {
            $nouveauStatut = 'EN_REMBOURSEMENT';
        }

        if ($nouveauStatut !== null && $nouveauStatut !== $statutActuel) {
            $this->update(['statut_global' => $nouveauStatut]);
            $aChange = true;
        }

        return $aChange;
    }

    /**
     * Scope SOURCE UNIQUE pour "dossier réellement en retard" : vérifie
     * l'état RÉEL des échéances (date dépassée, non intégralement réglée),
     * PAS la colonne statut_global qui peut être temporairement désynchronisée
     * (self-heal/cron pas encore repassé sur ce dossier précis).
     *
     * Bug corrigé le 14/09/2026 : la sidebar (compteur "Recouvrement Auto",
     * calculé ainsi) et la page "Liste des dossiers" (compteurs "En retard" /
     * filtre Statut=En retard, qui utilisaient `statut_global = 'EN_RETARD'`
     * directement) affichaient des totaux différents (7 vs 6) précisément à
     * cause de cet écart de fraîcheur. Utiliser CE scope PARTOUT où on doit
     * savoir si un dossier est en retard élimine la classe de bug entière —
     * plus besoin de dépendre du timing d'un cron/sync pour être exact.
     */
    public function scopeEnRetardReel($query)
    {
        $aujourdhui = now()->toDateString();

        return $query->whereNotIn('statut_global', ['SOLDE', 'ANNULE'])
            ->whereHas('echeancier.echeances', function ($q) use ($aujourdhui) {
                $q->whereIn('statut', ['EN_ATTENTE', 'EN_RETARD', 'PARTIELLEMENT_PAYE'])
                  ->where('date_echeance', '<', $aujourdhui);
            });
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

    public function agentAnalyse()
    {
        return $this->belongsTo(Agent::class, 'agent_analyse_matricule', 'matricule');
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

    public function getConditionsRetenuesAttribute(): array
    {
        $validations = $this->relationLoaded('validations')
            ? $this->validations
            : $this->validations()->get();

        $approvedValidations = $validations
            ->filter(fn (CreditValidation $validation) => in_array($validation->decision, ['APPROUVE', 'APPROUVE_AVEC_RESERVE'], true))
            ->sortByDesc('ordre_etape')
            ->values();

        $latestApproved = $approvedValidations->first();
        $latestAmount = $approvedValidations->first(fn (CreditValidation $validation) => $validation->montant_valide !== null);
        $latestDuration = $approvedValidations->first(fn (CreditValidation $validation) => $validation->duree_mois_validee !== null);

        return [
            'montant' => round((float) ($latestAmount?->montant_valide
                ?? $this->montant_approuve
                ?? $this->analyse?->montant_recommande
                ?? $this->montant_demande), 2),
            'duree_mois' => (int) ($latestDuration?->duree_mois_validee ?? $this->duree_mois),
            'source' => $latestApproved?->type_validateur ?? ($this->analyse?->montant_recommande ? 'ANALYSE' : 'DEMANDE'),
        ];
    }

    public function getDateSoumissionAttribute()
    {
        return $this->soumis_le;
    }

    public function getDateDeblocageAttribute()
    {
        return $this->deblocage?->debloque_le;
    }

    public function getAgentChargeAttribute()
    {
        return $this->agentAnalyse;
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
