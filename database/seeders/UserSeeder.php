<?php

namespace Database\Seeders;

use App\Models\EmployeeMODEL;
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
            'status' => 'active'
        ]);

        RoleMODEL::create([
            'role_name' => "Admin",
            'permission' => "All",
            'status' => "active",
        ]);

        EmployeeMODEL::create([
            'account_id' => 1,
            'firstname' => "Admin",
            'lastname' => "Surname",
            'contact' => "09611233221",
            'address' => "Admin Address",
            'country' => "Philippines",
            'state' => "Metro Manila",
            'city' => "Quezon City",
            'zip_code' => "1106",
            'department' => "Operations",
            'company' => "8nergy IT",
            'role_id' => 1,
        ]);


    }
}
