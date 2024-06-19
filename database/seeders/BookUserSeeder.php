<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
use Carbon\Carbon;

class BookUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        $loans = [];

        for ($i = 0; $i < 20; $i++) {
            $borrowedAt = $faker->dateTimeBetween('-1 month', 'now');
            $dueDate = (clone $borrowedAt)->modify('+14 days');

            $loans[] = [
                'user_id' => $faker->numberBetween(1, 20), // Adjust based on your actual user ID range
                'book_id' => $faker->numberBetween(1, 20), // Adjust based on your actual book ID range
                'borrowed_at' => $borrowedAt,
                'due_date' => $dueDate,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
        }

        DB::table('book_user')->insert($loans);
    }
}
