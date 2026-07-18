<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ActivityLog extends Model
{
    protected $table = 'tb_activity_logs';

    protected $fillable = [
        'module',
        'type_action',
        'loggable_type',
        'loggable_id',
        'reference',
        'acteur_matricule',
        'description',
        'anciennes_valeurs',
        'nouvelles_valeurs',
        'ip_address',
    ];

    protected $casts = [
        'anciennes_valeurs' => 'array',
        'nouvelles_valeurs' => 'array',
    ];

    // Labels lisibles par module/action (extensible)
    public static array $labels = [
        // Caisse
        'OPERATION_CREEE'    => 'Opération de caisse créée',
        'OPERATION_ANNULEE'  => 'Opération de caisse annulée',
        'DEPENSE_CREEE'      => 'Dépense de caisse enregistrée',
        'DEPENSE_ANNULEE'    => 'Dépense de caisse annulée',
        'RECETTE_CREEE'      => 'Recette de caisse enregistrée',
        'RECETTE_ANNULEE'    => 'Recette de caisse annulée',
        // Client
        'CLIENT_CREE'        => 'Création client',
        'CLIENT_MODIFIE'     => 'Modification client',
        'CLIENT_SUPPRIME'    => 'Suppression client',
        // Compte
        'COMPTE_CREE'        => 'Ouverture de compte',
        'COMPTE_SUPPRIME'    => 'Suppression de compte',
        // RH
        'AGENT_CREE'         => 'Création agent',
        'AGENT_MODIFIE'      => 'Modification agent',
        'AGENT_SUPPRIME'     => 'Suppression agent',
        'AFFECTATION_CREEE'  => 'Affectation créée',
        'AFFECTATION_ETAT'   => 'Changement état affectation',
        'AFFECTATION_SUPPRIMEE' => 'Suppression affectation',
        // Trésorerie
        'COFFRE_APPROVISIONNE' => 'Approvisionnement coffre',
        'GUICHET_ALIMENTE'     => 'Alimentation guichet depuis coffre',
        'CLOTURE_APPROUVEE'    => 'Clôture guichet approuvée',
        'CLOTURE_REJETEE'      => 'Clôture guichet rejetée',
        'CLOTURE_LIGNE_APPROUVEE' => 'Ligne de clôture approuvée',
        'CLOTURE_LIGNE_REJETEE'   => 'Ligne de clôture rejetée',
        // Administration (gestion des accès — module le plus sensible)
        'UTILISATEUR_CREE'        => 'Création utilisateur',
        'UTILISATEUR_MODIFIE'     => 'Modification utilisateur',
        'UTILISATEUR_SUPPRIME'    => 'Suppression utilisateur',
        'ROLE_CREE'                => 'Création rôle',
        'PERMISSION_ATTACHEE'      => 'Permission attribuée à un rôle',
        'PERMISSION_DETACHEE'      => 'Permission retirée d\'un rôle',
        'ROLE_ATTACHE_UTILISATEUR' => 'Rôle attribué à un utilisateur',
        'ROLE_DETACHE_UTILISATEUR' => 'Rôle retiré d\'un utilisateur',
        // Comptabilité — mapping dynamique dépenses
        'CATEGORIE_DEPENSE_CREEE'    => 'Catégorie de dépense créée',
        'CATEGORIE_DEPENSE_MODIFIEE' => 'Catégorie de dépense modifiée (mapping compte)',
        'CATEGORIE_DEPENSE_SUPPRIMEE'=> 'Catégorie de dépense supprimée',
        'CATEGORIE_RECETTE_CREEE'     => 'Catégorie de recette créée',
        'CATEGORIE_RECETTE_MODIFIEE'  => 'Catégorie de recette modifiée (mapping compte)',
        'CATEGORIE_RECETTE_SUPPRIMEE' => 'Catégorie de recette supprimée',
        'EXERCICE_PROPOSE_CLOTURE'    => "Proposition de clôture d'exercice",
        'EXERCICE_CLOTURE_VALIDEE'    => "Clôture d'exercice validée (définitive)",
        'EXERCICE_CLOTURE_REJETEE'    => "Proposition de clôture d'exercice rejetée",
    ];

    public function labelAction(): string
    {
        return self::$labels[$this->type_action] ?? $this->type_action;
    }

    /**
     * Enregistre une action dans le journal d'activité transversal.
     * Ne doit JAMAIS interrompre l'action métier en cas d'échec (try/catch).
     */
    public static function record(
        string $module,
        string $typeAction,
        ?object $loggable = null,
        ?string $reference = null,
        ?string $description = null,
        ?array $ancien = null,
        ?array $nouveau = null
    ): void {
        try {
            static::create([
                'module'            => $module,
                'type_action'       => $typeAction,
                'loggable_type'     => $loggable ? get_class($loggable) : null,
                'loggable_id'       => $loggable?->id,
                'reference'         => $reference,
                'acteur_matricule'  => Auth::user()?->agent?->matricule,
                'description'       => $description,
                'anciennes_valeurs' => $ancien,
                'nouvelles_valeurs' => $nouveau,
                'ip_address'        => request()->ip(),
            ]);
        } catch (\Throwable $e) {
            Log::warning('[ActivityLog] Échec enregistrement : ' . $e->getMessage());
        }
    }
}
