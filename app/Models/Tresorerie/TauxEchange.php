<?php

namespace App\Models\Tresorerie;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * TauxEchange
 * -----------
 * Table : tb_taux_echanges
 *
 * Historique des taux de change par paire de devises, avec une période de
 * validité explicite (date_debut / date_fin). Chaque changement de taux crée
 * une NOUVELLE ligne — aucune ligne existante n'est jamais modifiée (traçabilité
 * complète pour l'audit et la comptabilité).
 *
 * date_fin = NULL  → taux actif "jusqu'à nouvel ordre" (pas encore clôturé)
 * date_fin renseigné → taux historique, valide uniquement sur cette période.
 *
 * Utilisé par toutes les opérations multi-devises du système (Change au
 * guichet, Virement bancaire interdevises) via TauxEchange::actif().
 */
class TauxEchange extends Model
{
    protected $table = 'tb_taux_echanges';

    protected $fillable = [
        'devise_source', 'devise_destination', 'taux', 'date_debut', 'date_fin'
    ];

    protected $casts = [
        'taux'       => 'decimal:6',
        'date_debut' => 'datetime',
        'date_fin'   => 'datetime',
    ];

    public function deviseSource()
    {
        return $this->belongsTo(Devise::class, 'devise_source', 'code_iso');
    }

    public function deviseDestination()
    {
        return $this->belongsTo(Devise::class, 'devise_destination', 'code_iso');
    }

    /**
     * Horloge de référence pour "maintenant", ANCRÉE sur MySQL (et non sur PHP/Laravel).
     *
     * Pourquoi : PHP/Laravel utilise `config('app.timezone')` (UTC sur ce serveur) alors
     * que la session MySQL utilise sa timezone système ('SYSTEM', qui s'avère être
     * UTC+1 ici). Sans précaution, comparer un `now()` PHP à une colonne TIMESTAMP
     * remplie par MySQL produit un décalage d'1h qui rend un taux tout juste créé
     * "pas encore actif". En passant systématiquement par le NOW() de MySQL pour
     * TOUTES les comparaisons ET pour l'insertion de la date de début par défaut,
     * on reste dans un référentiel d'horloge unique et cohérent, sans jamais
     * mélanger les deux fuseaux horaires. Mise en cache pour la durée de la requête.
     */
    private static ?Carbon $dbNowCache = null;

    public static function dbNow(): Carbon
    {
        if (self::$dbNowCache === null) {
            self::$dbNowCache = Carbon::parse(DB::selectOne('SELECT NOW() as n')->n);
        }
        return self::$dbNowCache;
    }

    /**
     * Retourne le taux ACTIF pour une paire de devises à une date donnée
     * (par défaut : maintenant, selon l'horloge MySQL — voir dbNow()). Retourne
     * null si aucun taux actif n'est défini pour cette paire — dans ce cas,
     * l'opération concernée doit être bloquée.
     */
    public static function actif(string $deviseSource, string $deviseDestination, ?Carbon $date = null): ?self
    {
        $deviseSource = strtoupper($deviseSource);
        $deviseDestination = strtoupper($deviseDestination);
        $date = $date ?? self::dbNow();

        if ($deviseSource === $deviseDestination) {
            return null; // pas de conversion nécessaire, géré par l'appelant (taux = 1)
        }

        return static::where('devise_source', $deviseSource)
            ->where('devise_destination', $deviseDestination)
            ->where('date_debut', '<=', $date)
            ->where(function ($q) use ($date) {
                $q->whereNull('date_fin')->orWhere('date_fin', '>=', $date);
            })
            ->orderByDesc('date_debut')
            ->first();
    }

    /** Le taux est-il actuellement dans sa période de validité ? (horloge MySQL) */
    public function getEstActifAttribute(): bool
    {
        $now = self::dbNow();
        return $this->date_debut <= $now && ($this->date_fin === null || $this->date_fin >= $now);
    }
}
