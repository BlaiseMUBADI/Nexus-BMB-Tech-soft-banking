<?php

namespace App\Models\Clients;

use App\Models\Caisse\Transaction;
use Illuminate\Database\Eloquent\Model;

/**
 * Suivi de l'émission d'une carte membre physique.
 *
 * Statuts :
 *  - PAYEE     : le frais a été encaissé au guichet, la carte n'a pas encore été imprimée.
 *  - IMPRIMEE  : la carte a été imprimée au moins une fois.
 *  - REVOQUEE  : la carte a été invalidée (perte/vol) — le token de vérification n'est plus valide.
 */
class ClientCarte extends Model
{
    protected $table = 'tb_client_cartes';

    public const PAYEE = 'PAYEE';
    public const IMPRIMEE = 'IMPRIMEE';
    public const REVOQUEE = 'REVOQUEE';

    protected $fillable = [
        'client_matricule',
        'transaction_id',
        'montant_paye',
        'devise_code',
        'token_verification',
        'statut',
        'agent_encaissement_matricule',
        'guichet_id',
        'imprimee_le',
        'imprimee_par_matricule',
        'observations',
    ];

    protected $casts = [
        'montant_paye' => 'decimal:2',
        'imprimee_le' => 'datetime',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_matricule', 'matricule');
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class, 'transaction_id');
    }

    public static function genererToken(): string
    {
        do {
            $token = bin2hex(random_bytes(16));
        } while (self::where('token_verification', $token)->exists());

        return $token;
    }
}
