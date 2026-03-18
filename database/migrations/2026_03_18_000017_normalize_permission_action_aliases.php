<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $updates = [
            'EBEN-PER7'  => ['nom' => 'Creer/Ajouter agent', 'description' => 'Creer (ajouter) un nouvel agent'],
            'EBEN-PER8'  => ['nom' => 'Modifier/Supprimer agent-service-poste', 'description' => 'Modifier ou supprimer agent, service et poste RH'],
            'EBEN-PER9'  => ['nom' => 'Affectations (Creer/Modifier/Supprimer)', 'description' => 'Gerer les affectations: creer, modifier, supprimer'],
            'EBEN-PER10' => ['nom' => 'Voir caisse + Demandes', 'description' => 'Consulter la caisse et initier des demandes selon routes autorisees'],
            'EBEN-PER11' => ['nom' => 'Gerer operations caisse', 'description' => 'Creer, modifier, confirmer et annuler des operations caisse'],
            'EBEN-PER16' => ['nom' => 'Creer/Ajouter client', 'description' => 'Creer (ajouter) un client'],
            'EBEN-PER17' => ['nom' => 'Modifier/Supprimer client', 'description' => 'Modifier ou supprimer un client'],
            'EBEN-PER19' => ['nom' => 'Creer/Ajouter/Supprimer compte', 'description' => 'Creer (ajouter) ou supprimer un compte'],
            'EBEN-PER54' => ['nom' => 'Creer/Ajouter demande credit', 'description' => 'Creer (ajouter) une nouvelle demande de credit'],
            'EBEN-PER66' => ['nom' => 'Annuler/Supprimer dossier credit', 'description' => 'Annuler (supprimer) definitivement un dossier credit'],
            'EBEN-PER77' => ['nom' => 'Creer/Ajouter en tresorerie', 'description' => 'Creation/ajout d operations dans le module tresorerie'],
            'EBEN-PER78' => ['nom' => 'Modifier/Mettre a jour en tresorerie', 'description' => 'Modification/mise a jour d operations dans le module tresorerie'],
            'EBEN-PER79' => ['nom' => 'Supprimer/Annuler en tresorerie', 'description' => 'Suppression/annulation d operations dans le module tresorerie'],
        ];

        foreach ($updates as $code => $data) {
            DB::table('tb_permissions')
                ->where('code', $code)
                ->update([
                    'nom' => $data['nom'],
                    'description' => $data['description'],
                    'updated_at' => now(),
                ]);
        }
    }

    public function down(): void
    {
        $revert = [
            'EBEN-PER7'  => ['nom' => 'Créer agent', 'description' => "Création d'un nouvel agent"],
            'EBEN-PER8'  => ['nom' => 'Modifier agent', 'description' => "Modification d'un agent"],
            'EBEN-PER9'  => ['nom' => 'Affectations', 'description' => 'Gestion des affectations'],
            'EBEN-PER10' => ['nom' => 'Voir caisse', 'description' => 'Consultation des caisses'],
            'EBEN-PER11' => ['nom' => 'Ouvrir caisse', 'description' => "Ouverture d'une caisse/guichet"],
            'EBEN-PER16' => ['nom' => 'Créer client', 'description' => "Enregistrement d'un client"],
            'EBEN-PER17' => ['nom' => 'Modifier client', 'description' => "Modification d'un client"],
            'EBEN-PER19' => ['nom' => 'Créer compte', 'description' => "Ouverture d'un compte"],
            'EBEN-PER54' => ['nom' => 'Créer demande crédit', 'description' => 'Saisir une nouvelle demande de crédit'],
            'EBEN-PER66' => ['nom' => 'Annuler dossier crédit', 'description' => 'Annuler définitivement un dossier crédit'],
            'EBEN-PER77' => ['nom' => 'Ajouter en tresorerie', 'description' => 'Creation/ajout d operations dans le module tresorerie'],
            'EBEN-PER78' => ['nom' => 'Modifier en tresorerie', 'description' => 'Modification d operations dans le module tresorerie'],
            'EBEN-PER79' => ['nom' => 'Supprimer en tresorerie', 'description' => 'Suppression d operations dans le module tresorerie'],
        ];

        foreach ($revert as $code => $data) {
            DB::table('tb_permissions')
                ->where('code', $code)
                ->update([
                    'nom' => $data['nom'],
                    'description' => $data['description'],
                    'updated_at' => now(),
                ]);
        }
    }
};
