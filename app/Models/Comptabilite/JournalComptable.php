<?php

namespace App\Models\Comptabilite;

use App\Models\Caisse\Transaction;
use Illuminate\Database\Eloquent\Model;

class JournalComptable extends Model
{
    protected $table = 'tb_compta_journaux';

    protected $fillable = [
        'code_journal',
        'reference_piece',
        'transaction_id',
        'type_piece',
        'devise_code',
        'libelle',
        'statut',
        'agent_matricule',
        'date_ecriture',
        'metadata',
    ];

    protected $casts = [
        'date_ecriture' => 'datetime',
        'metadata' => 'array',
    ];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class, 'transaction_id');
    }

    public function ecritures()
    {
        return $this->hasMany(EcritureComptable::class, 'journal_id');
    }
}
