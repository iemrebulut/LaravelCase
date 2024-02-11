<?php

namespace App\Jobs;

use App\Events\OrderCompleted;
use App\Models\Discounts;
use App\Models\Orders;
use App\Models\Products;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class OrderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $data;
    public $discountData;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($data, $discountData)
    {
        $this->data = $data;
        $this->discountData = $discountData;

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $order = Orders::create($this->data);
        $this->discountData['orderId'] = $order->id;
        Discounts::create($this->discountData);  
        foreach(json_decode($this->data['items']) as $product) {
            $productQ = Products::find($product->productId);
            $productQ->stock = $productQ->stock - $product->quantity;
            $productQ->save();
        }
    }
}
