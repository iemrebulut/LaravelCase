<?php

namespace Database\Seeders;

use App\Models\Customers;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class CustomersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $json = Storage::disk('local')->get('json/customers.json');
        $customers = json_decode($json, true);

        foreach($customers as $customer) {
            Customers::query()->updateOrCreate([
                'name' => $customer['name'],
                'since' => $customer['since'],
                'revenue' => $customer['revenue']
            ]);
        } 
    }
}
