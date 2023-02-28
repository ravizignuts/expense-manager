<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
        //
            \App\Models\User::factory()->create([
                'firstname'      => 'Ravi',
                'lastname'       => 'Thakor',
                'email'          => 'ravit@gmail.com',
                'type'           => 'admin',
                'password'       => Hash::make('ravi1234'),
                // 'account_name'   => 'Ravi Thakor',
                // 'account_number' => '885557695091'
            ]);
            \App\Models\User::factory()->create([
                'firstname'      => 'Dinesh',
                'lastname'       => 'Prajapti',
                'email'          => 'dineshp@gmail.com',
                'type'           => 'admin',
                'password'       => Hash::make('dinesh12'),
            ]);
            \App\Models\User::factory()->create([
                'firstname'      => 'Bhawik',
                'lastname'       => 'Padhiaar',
                'email'          => 'bhawikp@gmail.com',
                'type'           => 'admin',
                'password'       => Hash::make('bhawik12'),
            ]);
    }
}
