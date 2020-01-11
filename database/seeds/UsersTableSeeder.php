<?php

use App\Models\User;
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
        $users = [
            [
                "name"     => "admin lazday",
                "username" => "admin",
                "email"    => "admin@lazday.com",
                "password" => bcrypt("P@ssword"),
                "created_at" => now(),
            ],
            [
                "name"     => "user",
                "username" => "user",
                "email"    => "usersn@lazday.com",
                "password" => bcrypt("satudelapankali"),
                "created_at" => now(),
            ],
        ];

        User::insert($users);
    }
}
