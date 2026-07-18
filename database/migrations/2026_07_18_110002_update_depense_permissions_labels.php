<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('tb_permissions')->where('code', 'EBEN-PER114')->update([
            'nom' => 'Enregistrer une opération administrative (dépense/recette)',
            'description' => 'Saisir une dépense (sortie) ou une recette (entrée) de caisse avec catégorie comptable OHADA et justificatif',
        ]);

        DB::table('tb_permissions')->where('code', 'EBEN-PER115')->update([
            'nom' => 'Gérer les catégories de dépenses/recettes',
            'description' => 'Créer/modifier/supprimer les catégories de dépenses et recettes et leur mapping vers le plan comptable OHADA',
        ]);
    }

    public function down(): void
    {
        // Pas de rollback destructif nécessaire (simple mise à jour de libellés)
    }
};
