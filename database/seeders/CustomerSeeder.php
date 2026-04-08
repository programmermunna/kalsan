<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Get the company user (created by UsersTableSeeder)
        $company = User::where('type', 'company')->first();
        
        if (!$company) {
            $this->command->warn('Company user not found. Please run UsersTableSeeder first.');
            return;
        }

        $travelAgencies = [
            [
                'name' => 'Seoul Travel Agency',
                'email' => 'seoul@travel.com',
                'contact' => '010-1234-5678',
                'type' => 'Travel Agency',
                'customer_id' => 1,
                'created_by' => $company->id,
                'is_active' => 1,
                'lang' => 'en',
                'balance' => 0.00,
            ],
            [
                'name' => 'Busan Travel Agency',
                'email' => 'busan@travel.com',
                'contact' => '010-2345-6789',
                'type' => 'Travel Agency',
                'customer_id' => 2,
                'created_by' => $company->id,
                'is_active' => 1,
                'lang' => 'en',
                'balance' => 0.00,
            ],
            [
                'name' => 'Jeju Travel Agency',
                'email' => 'jeju@travel.com',
                'contact' => '010-3456-7890',
                'type' => 'Travel Agency',
                'customer_id' => 3,
                'created_by' => $company->id,
                'is_active' => 1,
                'lang' => 'en',
                'balance' => 0.00,
            ],
            [
                'name' => 'Incheon Travel Agency',
                'email' => 'incheon@travel.com',
                'contact' => '010-4567-8901',
                'type' => 'Travel Agency',
                'customer_id' => 4,
                'created_by' => $company->id,
                'is_active' => 1,
                'lang' => 'en',
                'balance' => 0.00,
            ],
            [
                'name' => 'Gyeongju Travel Agency',
                'email' => 'gyeongju@travel.com',
                'contact' => '010-5678-9012',
                'type' => 'Travel Agency',
                'customer_id' => 5,
                'created_by' => $company->id,
                'is_active' => 1,
                'lang' => 'en',
                'balance' => 0.00,
            ],
        ];

        foreach ($travelAgencies as $agency) {
            // Check if customer already exists
            $existingCustomer = Customer::where('email', $agency['email'])
                ->where('created_by', $company->id)
                ->first();

            if (!$existingCustomer) {
                Customer::create($agency);
                $this->command->info('Created Travel Agency: ' . $agency['name']);
            } else {
                $this->command->warn('Travel Agency already exists: ' . $agency['name']);
            }
        }

        $this->command->info('Travel Agency demo data seeding completed!');
    }
}

