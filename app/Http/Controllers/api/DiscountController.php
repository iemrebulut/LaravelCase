<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\BaseController;
use App\Models\Campaigns;
use App\Models\Products;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class DiscountController extends BaseController
{
    public function __construct() {
        Cache::forget('campaigns');
        Cache::remember('campaigns', now()->addMinutes(3) , function() {
            $campaigns = Campaigns::with('campaignConditions')->withCount('campaignConditions')->get();
            if($campaigns->isEmpty())
                return null;
            return $campaigns;
        });
    }

    public function campaignCheck($cartData, $orderTotalPrice) {   
        $returnCampaign = null;
        $campaignPrice = 0;
        $discountAmount = 0; 

        if(Cache::has('campaigns')) {
            $campaigns = Cache::get('campaigns');
            foreach ($campaigns as $campaign) {  
                if($campaign->campaign_conditions_count > 0) {
                    
                    $productCount = Products::query();
                    $productCount->where('id', $cartData['productId']); 
                    foreach ($campaign->campaignConditions as $campaignCondition) { 
                        // Koşullar ile ürünleri eşleştir
                        $productCount->where($campaignCondition['key'], $campaignCondition['value']); 
                    } 
                }   
                if($campaign->campaign_conditions_count <= 0 OR $productCount->count() > 0) { 
                    if ($campaign->type == 3 AND ($campaign->quantity_min_limit <= $cartData['quantity'])) {
                        // X Al Y Öde kampanyası ise;
                        $campaignCount = $cartData['quantity']-$campaign->quantity_free;
                        $campaignPrice = $campaignCount * $cartData['unitPrice']; 
                        $discountAmount = $cartData['total']-$campaignPrice;
                    } else if (
                        ($campaign->type == 1 OR $campaign->type == 2) AND 
                        (
                            ($campaign->price_min_limit > 0 AND ($campaign->price_min_limit <= $cartData['total'])) OR 
                            ($campaign->quantity_min_limit > 0 AND ($campaign->quantity_min_limit <= $cartData['quantity']))
                        )
                    ) {
                        // Yüzde indirim kampanyası ise;
                        if($campaign->type == 1) {
                            // Ürüne % indirim yap;
                            $campaignPrice = ($cartData['quantity'] * ($cartData['unitPrice'] - ($cartData['unitPrice'] * $campaign->discount_percent)));
                            $discountAmount = $cartData['total']-$campaignPrice;
                        } else if($campaign->type == 2) {
                            // Siparişin totaline % indirim yap.
                            $campaignPrice = ($orderTotalPrice * $campaign->discount_percent);
                            $discountAmount = $campaignPrice;
                        }
                    }  

                    if(!is_array($returnCampaign) OR (is_array($returnCampaign) AND ($returnCampaign['discountAmount'] > $campaignPrice))) {
                        // Müşteri için en uygun teklif hazırlanıyor.
                        $returnCampaign = [
                            'discountReason' => $campaign->title,
                            'discountAmount' => round($discountAmount, 2), 
                            // 'subtotal' => round($subTotal, 2)
                        ];
                    }
                }
            }   
        }   
        return $returnCampaign;
    }    
}
