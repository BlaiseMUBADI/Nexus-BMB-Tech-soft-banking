<?php

namespace App\Models\Comptabilite;

use Illuminate\Database\Eloquent\Model;

class PlanComptable extends Model
{
    protected $table = 'tb_plan_comptable';
    protected $primaryKey = 'numero_compte';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'numero_compte',
        'classe_ohada',
        'libelle',
        'parent_compte',
        'niveau',
        'type_compte',
        'est_mouvementable',
        'est_actif',
    ];

    protected $casts = [
        'est_mouvementable' => 'boolean',
        'est_actif' => 'boolean',
        'niveau' => 'integer',
    ];

    public function ecritures()
    {
        return $this->hasMany(EcritureComptable::class, 'numero_compte', 'numero_compte');
    }
}
