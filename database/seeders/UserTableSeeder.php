<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use DB;


class UserTableSeeder extends Seeder
{
   
    public function run()
    {
         DB::table('users')->insert([
             'name'=>'admin',
             'email'=>'admin@yopmail.com',
             'username'=>'admin',
             'phone_number'=>7894561230,
             'password'=>Hash::make('mind@123'),
             'role_id'=>1
         ]);
    }
}
