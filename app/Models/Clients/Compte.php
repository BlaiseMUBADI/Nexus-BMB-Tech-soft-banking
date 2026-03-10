<?php

namespace App\Models\Clients;

use Illuminate\Database\Eloquent\Model;
use App\Models\Tresorerie\Portefeuille;

class Compte extends Model
{
    protected $table = 'tb_comptes';
    protected $primaryKey = 'code_compte';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = true;

    protected $fillable = [
        'code_compte',
        'client_matricule',
        'type',
        'solde_reel',
        'solde_bloque',
        'devise',
        'portefeuille_id',
    ];

    // Relation avec le client
    public function client()
    {
        return $this->belongsTo(Client::class, 'client_matricule', 'matricule');
    }

    public function portefeuille()
    {
        return $this->belongsTo(Portefeuille::class, 'portefeuille_id', 'id');
    }

    public static function boot()
    {
        parent::boot();
        static::creating(function ($compte) {
            if (!$compte->code_compte) {
                // Types valides : CC=Compte Courant, RMB=Remboursement, GTC=Caution, DAT=Dépôt à Terme, EAV=Épargne & Vie
                $typesValides = ['CC', 'RMB', 'GTC', 'DAT', 'EAV'];
                $type = $compte->type;
                if (!in_array($type, $typesValides)) {
                    throw new \Exception('Type de compte inconnu : ' . $type);
                }
                // Préfixe fixe : 243-52514-<TYPE>-
                $prefix = '243-52514-' . $type . '-';
                // Chercher le prochain numéro séquentiel pour ce type
                $existingAccounts = self::where('code_compte', 'like', $prefix . '%')->pluck('code_compte');
                $sequences = $existingAccounts->map(function($c) use ($prefix) {
                    $withoutPrefix = substr($c, strlen($prefix));
                    // Format attendu : 00001ABC (5 chiffres + 3 lettres)
                    return (int) substr($withoutPrefix, 0, 5);
                })->filter()->sort()->values();
                $nextSeq = 1;
                foreach ($sequences as $num) {
                    if ($num != $nextSeq) break;
                    $nextSeq++;
                }
                // 3 lettres aléatoires pour sécuriser
                $letters = strtoupper(substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 3));
                // Format final : 243-52514-CC-00001ABC
                $compte->code_compte = $prefix . str_pad($nextSeq, 5, '0', STR_PAD_LEFT) . $letters;
            }
        });
    }
}
