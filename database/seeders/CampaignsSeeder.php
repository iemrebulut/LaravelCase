<?php

namespace Database\Seeders;

use App\Models\Campaigns;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class CampaignsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $json = Storage::disk('local')->get('json/campaigns.json');
        $campaigns = json_decode($json, true);

        foreach($campaigns as $campaign) {
            Campaigns::query()->updateOrCreate([
                'title' => $campaign['title'],
                'type' => $campaign['type'], 
                'price_min_limit' => $campaign['price_min_limit'],
                'quantity_min_limit' => $campaign['quantity_min_limit'],
                'discount_percent' => $campaign['discount_percent'],
                'quantity_free' => $campaign['quantity_free']
            ]);
        } 
    }
}
