<?php

namespace Database\Seeders;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdministratorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $administrators = [
            [
                'name' => 'Surajit Sil',
                'email' => 'surajitsil2908@gmail.com',
                'password' => Hash::make("mySecretPassword"),
                'user_type' => 'admin',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        ];

        User::insert($administrators);
    }
}
