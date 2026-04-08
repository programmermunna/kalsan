<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DrivingLicense;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\User;

class DrivingLicenseSeeder extends Seeder
{
    public function run()
    {
        // Get some sample customers and invoices
        $customers = Customer::take(3)->get();
        $invoices = Invoice::take(3)->get();
        $creator = User::first();

        if ($customers->isEmpty() || $invoices->isEmpty() || !$creator) {
            $this->command->info('Skipping DrivingLicenseSeeder - missing required data');
            return;
        }

        $licenseData = [
            [
                'grade' => 'A1',
                'issue_date' => now()->subMonths(6),
                'expiry_date' => now()->addYears(5),
                'status' => 'active',
                'notes' => 'Completed advanced driving course',
            ],
            [
                'grade' => 'B',
                'issue_date' => now()->subMonths(3),
                'expiry_date' => now()->addYears(5),
                'status' => 'active',
                'notes' => 'Standard vehicle license',
            ],
            [
                'grade' => 'A2',
                'issue_date' => now()->subMonths(12),
                'expiry_date' => now()->addYears(5),
                'status' => 'active',
                'notes' => 'Motorcycle license',
            ],
        ];

        foreach ($customers as $index => $customer) {
            if (isset($licenseData[$index])) {
                $data = $licenseData[$index];
                $data['customer_id'] = $customer->id;
                $data['invoice_id'] = $invoices[$index]->id ?? null;
                $data['license_number'] = DrivingLicense::generateLicenseNumber();
                $data['serial_number'] = DrivingLicense::generateSerialNumber();
                $data['created_by'] = $creator->id;

                DrivingLicense::create($data);
            }
        }

        $this->command->info('DrivingLicenseSeeder completed successfully');
    }
}
