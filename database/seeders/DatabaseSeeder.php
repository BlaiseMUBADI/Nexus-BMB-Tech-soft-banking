<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            AdminSeeder::class,
        ]);
    }

    /**
     * Lance uniquement le jeu de données de test crédit.
     * Usage : php artisan db:seed --class=CreditTestSeeder
     */
    public function runCreditTest(): void
    {
        $this->call([
            CreditTestSeeder::class,
        ]);
    }
}
