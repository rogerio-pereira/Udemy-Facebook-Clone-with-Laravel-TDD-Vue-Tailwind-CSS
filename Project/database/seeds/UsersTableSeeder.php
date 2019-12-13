<?php

use App\User;
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
        factory(User::class)->create([
            'name' => 'Rogerio',
            'email' => 'rogerio@test.com',
            'password' => bcrypt('123456'),
        ]);
        
        factory(User::class)->create([
            'name' => 'Test',
            'email' => 'test@test.com',
            'password' => bcrypt('123456'),
        ]);
    }
}
