<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Admin;
use App\Models\Aircraft;
use App\Models\Books;
use App\Models\Facilities;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        Admin::factory(10)->create();
        Aircraft::factory(10)->create();
        Books::factory(10)->create();
        Facilities::factory(10)->create();
    }
}
