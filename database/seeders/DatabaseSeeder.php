<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        $this->call([
            TestCaseSeeder::class,
            TestStepSeeder::class,
            TestCaseTestStepOrderSeeder::class,
        ]);
        Schema::enableForeignKeyConstraints();

    }
}
