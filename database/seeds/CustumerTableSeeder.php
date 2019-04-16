<?php

use Illuminate\Database\Seeder;

class CustumerTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(\App\Custumer::class, 5)->create();
    }
}
