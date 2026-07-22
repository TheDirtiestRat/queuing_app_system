<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // generate the tickets and the windows
        DB::table('windows')->insert([
            "window_name" => "Window 1",
            "status" => "online",
        ]);
        DB::table('windows')->insert([
            "window_name" => "Window 2",
            "status" => "online",
        ]);

        for ($tickets = 1; $tickets <= 50; $tickets++) {
            DB::table('queue')->insert([
                "number" => str_pad($tickets, 3, '0', STR_PAD_LEFT),
                "type" => "A",
            ]);
        }
    }
}
