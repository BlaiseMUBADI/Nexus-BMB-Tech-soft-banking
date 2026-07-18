<?php

namespace App\Models\Caisse;

use Illuminate\Database\Eloquent\Model;
use App\Models\Comptabilite\CategorieRecette;
use App\Models\RH\Agent;

class Recette extends Model
{
    protected $table = 'tb_recettes';

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
        return $this->belongsTo(CategorieRecette::class, 'categorie_id');
    }

    public function agent()
    {
        return $this->belongsTo(Agent::class, 'agent_matricule', 'matricule');
    }
}
