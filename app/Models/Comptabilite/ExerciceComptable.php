<?php

namespace App\Models\Comptabilite;

use Illuminate\Database\Eloquent\Model;

class ExerciceComptable extends Model
{
    protected $table = 'tb_exercices_comptables';

    protected $fillable = [
        'annee', 'date_debut', 'date_fin', 'statut',
        'resultat_net_cloture', 'propose_par_matricule', 'propose_le',
        'valide_par_matricule', 'valide_le', 'rejete_par_matricule', 'rejete_le',
        'observations',
    ];

    protected $casts = [
        'date_debut' => 'date',
        'date_fin' => 'date',
        'resultat_net_cloture' => 'decimal:2',
        'propose_le' => 'datetime',
        'valide_le' => 'datetime',
        'rejete_le' => 'datetime',
    ];

    public function soldesOuverture()
    {
        return $this->hasMany(SoldeOuverture::class, 'exercice_id');
    }

    public static function ouvert(): ?self
    {
        return static::whereIn('statut', ['OUVERT', 'EN_ATTENTE_VALIDATION'])->orderByDesc('annee')->first();
    }

    public function estCloture(): bool
    {
        return $this->statut === 'CLOTURE';
    }

    /**
     * Trouve l'exercice comptable auquel appartient une date donnée.
     */
    public static function pourDate($date): ?self
    {
        $date = \Carbon\Carbon::parse($date)->toDateString();
        return static::whereDate('date_debut', '<=', $date)->whereDate('date_fin', '>=', $date)->first();
    }
}
