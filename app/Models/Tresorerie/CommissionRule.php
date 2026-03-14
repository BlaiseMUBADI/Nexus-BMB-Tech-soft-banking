<?php

namespace App\Models\Tresorerie;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class CommissionRule extends Model
{
    public const ALL = 'TOUS';
    public const TYPE_NO_ACCOUNT = 'SANS_COMPTE';
    public const MODE_FIXED = 'FIXE';
    public const MODE_PERCENTAGE = 'POURCENTAGE';

    protected $table = 'tb_commission_rules';

    protected $fillable = [
        'libelle',
        'code_operation',
        'type_compte',
        'type_guichet',
        'devise_code',
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
        'created_by_agent',
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
            ->whereDate('date_debut', '<=', $date)
            ->where(function (Builder $subQuery) use ($date) {
                $subQuery->whereNull('date_fin')
                    ->orWhereDate('date_fin', '>=', $date);
            });
    }

    public function portefeuille()
    {
        return $this->belongsTo(Portefeuille::class, 'portefeuille_id');
    }

    public function devise()
    {
        return $this->belongsTo(Devise::class, 'devise_code', 'code_iso');
    }

    public static function operationChoices(): array
    {
        return [
            self::ALL,
            \App\Models\Caisse\Transaction::DEPOT,
            \App\Models\Caisse\Transaction::RETRAIT,
            \App\Models\Caisse\Transaction::VIREMENT,
            \App\Models\Caisse\Transaction::CHANGE,
            \App\Models\Caisse\Transaction::PAIEMENT,
            \App\Models\Caisse\Transaction::REMBOURSEMENT,
        ];
    }

    public static function accountTypeChoices(): array
    {
        return [
            self::ALL,
            self::TYPE_NO_ACCOUNT,
            'CC',
            'RMB',
            'GTC',
            'DAT',
            'EAV',
        ];
    }

    public static function guichetTypeChoices(): array
    {
        return [self::ALL, 'FIXE', 'MOBILE', 'CENTRAL'];
    }
}