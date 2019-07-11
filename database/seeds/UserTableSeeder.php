<?php

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use App\Models\User;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
       $faker = Faker::create();

       	foreach (range(6, 10) as $index) {
   	        User::create([
   	            'username' => $faker->userName,
   	            'name' => $faker->name,
                'email' => $faker->email,
                'password' => app('hash')->make('password'),
   	            'status' => '1',
   	            'last_ip' => '127.0.0.1',
   	        ]);
       	}
    }
}
