<?php

namespace App\Models\Comptabilite;

use App\Models\Caisse\Transaction;
use App\Models\Clients\Client;
use App\Models\Clients\Compte;
use App\Models\RH\Agent;
use Illuminate\Database\Eloquent\Model;

/**
 * DemandeVirement
 * ----------------
 * Table : tb_demandes_virement
 *
 * Demande de virement bancaire entre deux comptes clients (même client ou
 * clients différents), avec ou sans changement de devise. Proposée par le
 * Comptable (EBEN-PER119), validée/rejetée par le Gérant ou le Directeur
 * (EBEN-PER120). Aucun mouvement d'argent tant que la demande n'est pas
 * approuvée.
 */
class DemandeVirement extends Model
{
    protected $table = 'tb_demandes_virement';
    public $timestamps = true;

    const EN_ATTENTE = 'EN_ATTENTE';
    const APPROUVEE  = 'APPROUVEE';
    const REJETEE    = 'REJETEE';

    protected $fillable = [
        'client_source_matricule',
        'compte_source_code',
        'montant_source',
        'commission_totale',
        'devise_source',
        'client_dest_matricule',
        'compte_dest_code',
        'montant_dest',
        'devise_dest',
        'taux_change',
        'motif',
        'statut',
        'comptable_matricule',
        'propose_le',
        'validateur_matricule',
        'commentaire_validateur',
        'traite_le',
        'transaction_id',
    ];

    protected $casts = [
        'montant_source' => 'decimal:2',
        'commission_totale' => 'decimal:2',
        'montant_dest'   => 'decimal:2',
        'taux_change'    => 'decimal:6',
        'propose_le'     => 'datetime',
        'traite_le'      => 'datetime',
    ];

    // ── Relations ────────────────────────────────────────────────

    public function clientSource()
    {
        return $this->belongsTo(Client::class, 'client_source_matricule', 'matricule');
    }

    public function clientDest()
    {
        return $this->belongsTo(Client::class, 'client_dest_matricule', 'matricule');
    }

    public function compteSource()
    {
        return $this->belongsTo(Compte::class, 'compte_source_code', 'code_compte');
    }

    public function compteDest()
    {
        return $this->belongsTo(Compte::class, 'compte_dest_code', 'code_compte');
    }

    public function comptable()
    {
        return $this->belongsTo(Agent::class, 'comptable_matricule', 'matricule');
    }

    public function validateur()
    {
        return $this->belongsTo(Agent::class, 'validateur_matricule', 'matricule');
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class, 'transaction_id', 'id');
    }

    // ── Helpers ──────────────────────────────────────────────────

    public function estMemeDevise(): bool
    {
        return $this->devise_source === $this->devise_dest;
    }

    public function statutLabel(): string
    {
        return match ($this->statut) {
            self::EN_ATTENTE => 'En attente',
            self::APPROUVEE  => 'Approuvée',
            self::REJETEE    => 'Rejetée',
            default          => $this->statut,
        };
    }

    public function statutBadgeClass(): string
    {
        return match ($this->statut) {
            self::EN_ATTENTE => 'badge-warning',
            self::APPROUVEE  => 'badge-success',
            self::REJETEE    => 'badge-danger',
            default          => 'badge-light',
        };
    }
}
