<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Settings;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        Settings::create([
            'name' => 'mode',
        ]);

        Settings::create([
            'name' => 'queue',
            'value' => false
        ]);

    }
}
