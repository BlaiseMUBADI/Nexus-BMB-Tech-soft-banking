<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->guardClientsIdentityDuplicates();
        $this->guardClientsEmailDuplicates();
        $this->guardComptesDuplicates();

        Schema::table('tb_clients', function (Blueprint $table) {
            if (!$this->hasIndex('tb_clients', 'uq_tb_clients_piece_type_num')) {
                $table->unique(['type_piece_identite', 'numero_piece_identite'], 'uq_tb_clients_piece_type_num');
            }

            if (!$this->hasIndex('tb_clients', 'uq_tb_clients_email')) {
                $table->unique('email', 'uq_tb_clients_email');
            }
        });

        Schema::table('tb_comptes', function (Blueprint $table) {
            if (!$this->hasIndex('tb_comptes', 'uq_tb_comptes_client_type_devise')) {
                $table->unique(['client_matricule', 'type', 'devise'], 'uq_tb_comptes_client_type_devise');
            }
        });
    }

    public function down(): void
    {
        Schema::table('tb_comptes', function (Blueprint $table) {
            if ($this->hasIndex('tb_comptes', 'uq_tb_comptes_client_type_devise')) {
                $table->dropUnique('uq_tb_comptes_client_type_devise');
            }
        });

        Schema::table('tb_clients', function (Blueprint $table) {
            if ($this->hasIndex('tb_clients', 'uq_tb_clients_email')) {
                $table->dropUnique('uq_tb_clients_email');
            }

            if ($this->hasIndex('tb_clients', 'uq_tb_clients_piece_type_num')) {
                $table->dropUnique('uq_tb_clients_piece_type_num');
            }
        });
    }

    private function guardClientsIdentityDuplicates(): void
    {
        $duplicates = DB::table('tb_clients')
            ->select('type_piece_identite', 'numero_piece_identite', DB::raw('COUNT(*) as total'))
            ->whereNotNull('numero_piece_identite')
            ->where('numero_piece_identite', '<>', '')
            ->groupBy('type_piece_identite', 'numero_piece_identite')
            ->havingRaw('COUNT(*) > 1')
            ->limit(5)
            ->get();

        if ($duplicates->isNotEmpty()) {
            $sample = $duplicates
                ->map(fn ($row) => "{$row->type_piece_identite}/{$row->numero_piece_identite} ({$row->total})")
                ->implode(', ');

            throw new RuntimeException(
                'Migration bloquee: doublons detectes dans tb_clients (type_piece_identite, numero_piece_identite). Echantillon: ' . $sample
            );
        }
    }

    private function guardClientsEmailDuplicates(): void
    {
        $duplicates = DB::table('tb_clients')
            ->select('email', DB::raw('COUNT(*) as total'))
            ->whereNotNull('email')
            ->where('email', '<>', '')
            ->groupBy('email')
            ->havingRaw('COUNT(*) > 1')
            ->limit(5)
            ->get();

        if ($duplicates->isNotEmpty()) {
            $sample = $duplicates
                ->map(fn ($row) => "{$row->email} ({$row->total})")
                ->implode(', ');

            throw new RuntimeException(
                'Migration bloquee: doublons detectes dans tb_clients.email. Echantillon: ' . $sample
            );
        }
    }

    private function guardComptesDuplicates(): void
    {
        $duplicates = DB::table('tb_comptes')
            ->select('client_matricule', 'type', 'devise', DB::raw('COUNT(*) as total'))
            ->groupBy('client_matricule', 'type', 'devise')
            ->havingRaw('COUNT(*) > 1')
            ->limit(5)
            ->get();

        if ($duplicates->isNotEmpty()) {
            $sample = $duplicates
                ->map(fn ($row) => "{$row->client_matricule}/{$row->type}/{$row->devise} ({$row->total})")
                ->implode(', ');

            throw new RuntimeException(
                'Migration bloquee: doublons detectes dans tb_comptes (client_matricule, type, devise). Echantillon: ' . $sample
            );
        }
    }

    private function hasIndex(string $table, string $indexName): bool
    {
        return DB::table('information_schema.statistics')
            ->where('table_schema', DB::getDatabaseName())
            ->where('table_name', $table)
            ->where('index_name', $indexName)
            ->exists();
    }
};
