<?php

namespace App\Models\Credit;

use Illuminate\Database\Eloquent\Model;

class CreditPiece extends Model
{
    protected $table = 'tb_credit_pieces';

    protected $fillable = [
        'credit_demande_id',
        'libelle',
        'type_piece',
        'nom_fichier',
        'est_recu',
        'est_conforme',
        'observations',
    ];

    protected $casts = [
        'est_recu'      => 'boolean',
        'est_conforme'  => 'boolean',
    ];

    public function demande()
    {
        return $this->belongsTo(CreditDemande::class, 'credit_demande_id');
    }

    // Accessors de compatibilite pour vues
    public function getFourniAttribute(): bool
    {
        return (bool) $this->est_recu;
    }

    public function getReferenceAttribute(): ?string
    {
        return $this->nom_fichier;
    }

    public function getCommentaireAttribute(): ?string
    {
        return $this->observations;
    }
}
