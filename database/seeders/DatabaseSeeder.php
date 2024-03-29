<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Admin;
use App\Models\Aircraft;
use App\Models\Books;
use App\Models\Facilities;
use App\Models\Events;
use App\Models\Ships;
use App\Models\Weapons;
use App\Models\Vehicles;
use App\Models\User;
use App\Models\Stories;
use App\Models\Discussions;
use App\Models\Feedbacks;
use App\Models\Histories;

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
        Events::factory(10)->create();
        Ships::factory(10)->create();
        Weapons::factory(10)->create();
        Vehicles::factory(10)->create();
        User::factory(10)->create();
        Stories::factory(10)->create();
        Discussions::factory(10)->create();
        Feedbacks::factory(10)->create();
        Histories::factory(10)->create();
    }
}
