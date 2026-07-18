<?php

namespace App\Models\Comptabilite;

use Illuminate\Database\Eloquent\Model;

class SoldeOuverture extends Model
{
    protected $table = 'tb_soldes_ouverture';

    protected $fillable = ['exercice_id', 'numero_compte', 'solde_ouverture'];

    protected $casts = ['solde_ouverture' => 'decimal:2'];

    public function exercice()
    {
        return $this->belongsTo(ExerciceComptable::class, 'exercice_id');
    }

    public function compte()
    {
        return $this->belongsTo(PlanComptable::class, 'numero_compte', 'numero_compte');
    }
}
