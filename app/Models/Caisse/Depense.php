<?php

namespace App\Models\Caisse;

use Illuminate\Database\Eloquent\Model;
use App\Models\Comptabilite\CategorieDepense;
use App\Models\RH\Agent;

class Depense extends Model
{
    protected $table = 'tb_depenses';

    protected $fillable = [
        'transaction_id',
        'categorie_id',
        'motif',
        'piece_justificative',
        'agent_matricule',
    ];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class, 'transaction_id');
    }

    public function categorie()
    {
        return $this->belongsTo(CategorieDepense::class, 'categorie_id');
    }

    public function agent()
    {
        return $this->belongsTo(Agent::class, 'agent_matricule', 'matricule');
    }
}
