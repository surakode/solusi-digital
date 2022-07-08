<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users_acls')->insert([
            'id' => null,
            'name' => 'adminstrator',
            'dashboard' => 'crudie',

        ]);

        DB::table('users_acls')->insert([
            'id' => null,
            'name' => 'Super User',
            'dashboard' => 'crudie',
        ]);

        DB::table('users_acls')->where('id',2)->update(['id'=>0]);

        DB::table('users')->insert([
            'id'=> null,
            'name'=> 'administrator',
            'username'=> 'admin',
            'email'=> 'admin@coba.com',
            'mobile'=> '6282244181448',
            'password'=>  Hash::make('tidaktahu'),
            'device_key'=> '',
            'status'=> '1',
            'failed_login'=> '0',
            'last_login'=> null,
            'email_verified_at'=> null,
            'sudo'=> '1',
            'users_acls_id'=> '1',
        ]);

    }
}
