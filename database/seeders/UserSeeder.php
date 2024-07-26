<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'id'=>Str::uuid()->toString(),
            'username'=>'ammaralazii',
            'first_name'=>'Ammar',
            'last_name'=>'Alazii',
            'password'=>'1q2w3e4r5T*',
            'freeIPA'=>false
        ]);
    }
}
