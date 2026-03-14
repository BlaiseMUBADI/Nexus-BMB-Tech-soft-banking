<?php

namespace App\Models\Comptabilite;

use Illuminate\Database\Eloquent\Model;

class EcritureComptable extends Model
{
    protected $table = 'tb_compta_ecritures';

    protected $fillable = [
        'journal_id',
        'numero_compte',
        'devise_code',
        'libelle_ligne',
        'debit',
        'credit',
        'ordre',
    ];

    protected $casts = [
        'debit' => 'decimal:2',
        'credit' => 'decimal:2',
    ];

    public function journal()
    {
        return $this->belongsTo(JournalComptable::class, 'journal_id');
    }

    public function compte()
    {
        return $this->belongsTo(PlanComptable::class, 'numero_compte', 'numero_compte');
    }
}
