<?php

namespace Database\Seeders;

use App\Models\Products;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class ProductsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $json = Storage::disk('local')->get('json/products.json');
        $products = json_decode($json, true);

        foreach($products as $product) {
            Products::query()->updateOrCreate([
                'id' => $product['id'],
                'name' => $product['name'],
                'category' => $product['category'],
                'price' => $product['price'],
                'stock' => $product['stock']
            ]);
        }
    }
}
