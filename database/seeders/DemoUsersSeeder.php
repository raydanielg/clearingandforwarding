<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Client;
use Spatie\Permission\Models\Role;

class DemoUsersSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            'Super Admin' => [
                'name' => 'Super Admin',
                'email' => 'admin@caf.co.tz',
                'password' => 'password',
            ],
            'Admin' => [
                'name' => 'Amina Hassan',
                'email' => 'admin2@caf.co.tz',
                'password' => 'password',
            ],
            'Operations Manager' => [
                'name' => 'Juma Mwakyusa',
                'email' => 'ops@caf.co.tz',
                'password' => 'password',
            ],
            'Declaration Officer' => [
                'name' => 'Grace Mushi',
                'email' => 'declaration@caf.co.tz',
                'password' => 'password',
            ],
            'Clearing Officer' => [
                'name' => 'Peter Lyimo',
                'email' => 'clearing@caf.co.tz',
                'password' => 'password',
            ],
            'Transport Officer' => [
                'name' => 'Joseph Kimaro',
                'email' => 'transport@caf.co.tz',
                'password' => 'password',
            ],
            'Accountant' => [
                'name' => 'Sarah Mwakalukwa',
                'email' => 'accounts@caf.co.tz',
                'password' => 'password',
            ],
            'Cashier' => [
                'name' => 'David Massawe',
                'email' => 'cashier@caf.co.tz',
                'password' => 'password',
            ],
            'Insurance Officer' => [
                'name' => 'Rehema Shilla',
                'email' => 'insurance@caf.co.tz',
                'password' => 'password',
            ],
            'Branch Manager' => [
                'name' => 'Frank Mbwana',
                'email' => 'branch@caf.co.tz',
                'password' => 'password',
            ],
        ];

        foreach ($roles as $roleName => $info) {
            $user = User::firstOrCreate(
                ['email' => $info['email']],
                [
                    'name' => $info['name'],
                    'password' => bcrypt($info['password']),
                    'is_active' => true,
                ]
            );
            $user->assignRole($roleName);
        }

        // ── Demo Client ──
        $demoClient = Client::firstOrCreate(
            ['email' => 'client@demo.co.tz'],
            [
                'code' => 'CL-0001',
                'name' => 'Demo Trading Company Ltd',
                'tin' => '123-456-789',
                'vrn' => 'VRN-001',
                'type' => 'company',
                'phone' => '+255 754 123 456',
                'address' => 'Plot 123, Industrial Area, Dar es Salaam',
                'credit_limit' => 50000000,
                'payment_terms_days' => 30,
                'is_active' => true,
                'created_by' => 1,
            ]
        );

        $clientUser = User::firstOrCreate(
            ['email' => 'client@demo.co.tz'],
            [
                'name' => 'Demo Client',
                'password' => bcrypt('password'),
                'is_active' => true,
                'client_id' => $demoClient->id,
            ]
        );
        $clientUser->assignRole('Client');
    }
}
