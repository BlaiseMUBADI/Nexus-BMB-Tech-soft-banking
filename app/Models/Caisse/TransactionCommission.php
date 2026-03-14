<?php

namespace App\Models\Caisse;

use Illuminate\Database\Eloquent\Model;
use App\Models\Tresorerie\CommissionRule;

class TransactionCommission extends Model
{
    protected $table = 'tb_transaction_commissions';

    protected $fillable = [
        'transaction_id',
        'commission_rule_id',
        'libelle',
        'code_operation',
        'type_compte',
        'type_guichet',
        'devise_code',
        'code_zone',
        'portefeuille_id',
        'mode_calcul',
        'valeur_snapshot',
        'base_calcul',
        'montant_commission',
        'date_application',
        'agent_matricule',
        'guichet_id',
    ];

    protected $casts = [
        'valeur_snapshot' => 'decimal:4',
        'base_calcul' => 'decimal:2',
        'montant_commission' => 'decimal:2',
        'date_application' => 'datetime',
    ];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class, 'transaction_id');
    }

    public function rule()
    {
        return $this->belongsTo(CommissionRule::class, 'commission_rule_id');
    }
}