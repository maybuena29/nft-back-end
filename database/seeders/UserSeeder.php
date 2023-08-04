<?php

namespace Database\Seeders;

use App\Models\RoleMODEL;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            // 'username'=>'admin',
            'email'=>'admin@gmail.com',
            'password'=>bcrypt('admin123'),
        ]);


    }
}
