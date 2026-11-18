<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        $this->call([
            MemberSeeder::class,
        ]);

        \App\Models\Book::firstOrCreate(['id' => 1], [
            'title' => 'Laskar Pelangi',
            'author' => 'Andrea Hirata',
            'is_available' => true,
        ]);

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}
