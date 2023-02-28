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
                'phone'          => '9876543210',
                'type'           => 'admin',
                'password'       => Hash::make('ravi1234'),
                'account_name'   => 'Ravi Thakor',
                'account_number' => '885557695091'
            ]);
            \App\Models\User::factory()->create([
                'firstname'      => 'Dinesh',
                'lastname'       => 'Prajapti',
                'email'          => 'dineshp@gmail.com',
                'phone'          => '9876543211',
                'type'           => 'admin',
                'password'       => Hash::make('dinesh12'),
                'account_name'   => 'Dinesh Prajapti',
                'account_number' => '884327695091'
            ]);
            \App\Models\User::factory()->create([
                'firstname'      => 'Bhawik',
                'lastname'       => 'Padhiaar',
                'email'          => 'bhawikp@gmail.com',
                'phone'          => '9844543210',
                'type'           => 'admin',
                'password'       => Hash::make('bhawik12'),
                'account_name'   => 'Bhawik Padhiaar',
                'account_number' => '694582376115'
            ]);
    }
}
