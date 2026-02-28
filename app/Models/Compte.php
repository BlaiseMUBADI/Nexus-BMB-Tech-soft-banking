<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


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
    ];

    // Relation avec le client
    public function client()
    {
        return $this->belongsTo(Client::class, 'client_matricule', 'matricule');
    }

    public static function boot()
    {
        parent::boot();
        static::creating(function ($compte) {
            if (!$compte->code_compte) {
                $annee = date('y');
                $prefix_fixe = 'CMPT-EBENKGA-' . $annee . '-';
                $existingAccounts = self::where('code_compte', 'like', $prefix_fixe . '%')
                    ->pluck('code_compte');
                $sequences = $existingAccounts->map(function($c) {
                    return (int) substr($c, -5);
                })->filter()->sort()->values();
                $nextSeq = 1;
                foreach ($sequences as $num) {
                    if ($num != $nextSeq) break;
                    $nextSeq++;
                }
                $sel = strtoupper(\Illuminate\Support\Str::random(4));
                $compte->code_compte = $prefix_fixe . $sel . '-' . str_pad($nextSeq, 5, '0', STR_PAD_LEFT);
            }
            // Suppression de la génération du champ numero
        });
    }
}
