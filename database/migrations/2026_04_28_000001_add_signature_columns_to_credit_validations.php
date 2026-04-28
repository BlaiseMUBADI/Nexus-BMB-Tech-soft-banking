<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tb_credit_validations', function (Blueprint $table) {
            $table->string('signature_agent', 50)->nullable()->after('valide_le')
                ->comment('Matricule de l\'agent signataire (auto)');
            $table->string('nom_signataire', 150)->nullable()->after('signature_agent')
                ->comment('Nom complet du signataire au moment de la validation');
            $table->string('ip_validation', 45)->nullable()->after('nom_signataire')
                ->comment('Adresse IP depuis laquelle la validation a été soumise');
        });
    }

    public function down(): void
    {
        Schema::table('tb_credit_validations', function (Blueprint $table) {
            $table->dropColumn(['signature_agent', 'nom_signataire', 'ip_validation']);
        });
    }
};
