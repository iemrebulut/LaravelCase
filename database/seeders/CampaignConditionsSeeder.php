<?php

namespace Database\Seeders;

use App\Models\CampaignCondition;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class CampaignConditionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $json = Storage::disk('local')->get('json/campaignConditions.json');
        $campaignConditions = json_decode($json, true);

        foreach($campaignConditions as $campaignCondition) {
            CampaignCondition::query()->updateOrCreate([
                'campaign_id' => $campaignCondition['campaign_id'],
                'key' => $campaignCondition['key'],
                'value' => $campaignCondition['value'],
            ]);
        } 
    }
}
