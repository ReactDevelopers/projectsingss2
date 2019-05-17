<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\User::class, 50)->create();

        \App\User::create([
            'name' => 'Manish Mahant',
            'email' => 'mmanish904@gmail.com',
            'password' => bcrypt('secret'),
            'is_admin' => true,
            'remember_token' => str_random(10),
        ]);
    }
}
