<?php

namespace App\Models\Credit;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class CreditCommissionRule extends Model
{
    public const TOUS = 'TOUS';
    public const MODE_FIXED = 'FIXE';
    public const MODE_PERCENTAGE = 'POURCENTAGE';

    protected $table = 'tb_credit_commission_rules';

    protected $fillable = [
        'libelle',
        'devise_code',
        'type_credit',
        'code_zone',
        'portefeuille_id',
        'montant_min',
        'montant_max',
        'mode_calcul',
        'valeur',
        'priorite',
        'date_debut',
        'date_fin',
        'est_actif',
        'observations',
    ];

    protected $casts = [
        'montant_min' => 'decimal:2',
        'montant_max' => 'decimal:2',
        'valeur' => 'decimal:4',
        'date_debut' => 'date',
        'date_fin' => 'date',
        'est_actif' => 'boolean',
    ];

    public function scopeActiveOn(Builder $query, $date): Builder
    {
        $date = is_string($date) ? $date : $date->toDateString();

        return $query->where('est_actif', true)
            ->where(function (Builder $subQuery) use ($date) {
                $subQuery->whereNull('date_debut')
                    ->orWhereDate('date_debut', '<=', $date);
            })
            ->where(function (Builder $subQuery) use ($date) {
                $subQuery->whereNull('date_fin')
                    ->orWhereDate('date_fin', '>=', $date);
            });
    }

    public function portefeuille()
    {
        return $this->belongsTo(\App\Models\Tresorerie\Portefeuille::class, 'portefeuille_id');
    }

    public function zone()
    {
        return $this->belongsTo(\App\Models\Zone::class, 'code_zone', 'code_zone');
    }

    public static function typeCreditChoices(): array
    {
        return [
            self::TOUS,
            'INDIVIDUEL',
            'SOLIDAIRE',
            'PME',
        ];
    }

    public static function modeCalculChoices(): array
    {
        return [
            self::MODE_FIXED,
            self::MODE_PERCENTAGE,
        ];
    }
}
