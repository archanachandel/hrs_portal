<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        
        DB::table('users')->insert([
            'name' => 'admin',
            'email' => 'admin@yopmail.com',
            'username'=>'admin',
            'phone_number'=>7894561230,
            'password' => Hash::make('mind@123'),
             'role_id'=>1
        ]);
    }
}
