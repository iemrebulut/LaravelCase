<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\BaseController;
use App\Jobs\OrderJob;
use App\Models\Discounts;
use App\Models\Orders;
use App\Models\Products;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class OrderController extends BaseController
{

    public function __construct()
    { 
        Cache::forget('products');
        Cache::remember('products', now()->addMinutes(3), function() {
            return Products::get();
        });
    }

    public function list ($orderId)
    {  
        $order = Orders::where('id', $orderId)->first(); 
        if($order) {
            $order['items'] = json_decode($order['items']);
            return $this->success('Order detail', $order);
        } else {
            return $this->error('Couldn\'t find the order.', $orderId);
        } 
    }

    public function orderDiscountDetail ($orderId) 
    {   
        $discounts = Discounts::where('orderId', $orderId)->first();
        if($discounts) {
            $discounts['discounts'] = json_decode($discounts['discounts']);
            return $this->success('Discount detail', $discounts);
        } else {
            return $this->error('Couldn\'t find the discount.', $orderId);
        } 
    }

    public function create(Request $request) 
    {
        try {
            $requestData = $request->except('');

            $noProduct = array();
            $outOfStock = array();
            $cartData = array();
            $campaignController = new DiscountController();
            $sipNo = 'ORD-' . preg_replace("/[^0-9]/", "", uniqid());
            $user = Auth::user();  

            foreach ($requestData as $requestDataRow) {
                // Does the product id from the request exist in the database
                if(Cache::get('products')->where('id', $requestDataRow['productId'])->count() == 0 ) {$noProduct[] = $requestDataRow['productId'];}
                // Stock Control
                if(Cache::get('products')->where('id', $requestDataRow['productId'])->where('stock', '>=', $requestDataRow['quantity'])->count() == 0 ) {$outOfStock[] = $requestDataRow['productId'];}
            }

            if (count($noProduct) > 0){
                $data = [
                    'no_find_product_ids' => $noProduct
                ];
                return $this->error('Couldn\'t find the product.', $data);
            } else if (count($outOfStock) > 0){
                $data = [
                    'out_of_stock_product_ids' => $outOfStock
                ];
                return $this->error('The product sold out.', $data);
            }

            $orderTotalPrice = 0;
            foreach ($requestData as $key => $row) {

                $productDetail = Cache::get('products')->where('id', $row['productId'])->first(); 
 
                $returnProductDetail['products'][$productDetail['id']] = $productDetail;

                $orderTotalPrice += $productDetail['price']*$row['quantity'];

                $cartData['products'][$row['productId']] = array(
                    'productId' => $row['productId'],
                    'quantity' => $row['quantity'],
                    'unitPrice' => $productDetail['price'],
                    'total' => round($productDetail['price']*$row['quantity'], 2)
                );  
            }         
            
            foreach($cartData['products'] as $cartRow) {   
                $campaignsCheck = $campaignController->campaignCheck($cartRow, $orderTotalPrice); 

                if($campaignsCheck) { 
                    $campaigns[$cartRow['productId']] = $campaignsCheck;
                }
            }

            $totalDiscount = 0;
            $subTotal = $orderTotalPrice;
            foreach($campaigns as $key => $campaignRow) {  
                $subTotal -= $campaignRow['discountAmount'];
                $campaigns[$key]['subtotal'] = $subTotal;
                $totalDiscount += $campaignRow['discountAmount'];
            } 
 
            $orderData = array(
                'sip_no' => $sipNo, 
                'customerId' => $user->id,
                'items' => json_encode(array_values($cartData['products'])),
                'total' => round($orderTotalPrice, 2)
            );

            $discountsData = array(
                'discounts' => json_encode(array_values($campaigns)),
                'totalDiscount' => round($totalDiscount, 2),
                'discountedTotal' => round($orderTotalPrice-$totalDiscount, 2)
            );
 

            $orderCreate = dispatch(new OrderJob($orderData, $discountsData)); 


            return $this->success('Order created successfully', ['sip_no' => $sipNo]);

        } catch (\Exception $e) {
            return $this->error($e->getMessage(), []);
        }
    }

    public function delete (Request $request) 
    { 
        $requestData = $request->except(''); 
        $orderId = $requestData[0]['orderId'];

        $order = Orders::find($orderId); 
        if($order) {
            foreach(json_decode($order->items) as $product) {
                $productQ = Products::find($product->productId);
                $productQ->stock = $productQ->stock + $product->quantity;
                $productQ->save();
            }

            $order->discounts()->delete(); 
            $order->delete();

            return $this->success('Order deleted successfully');
        } else {
            return $this->error('Couldn\'t find the order.', $orderId);
        } 
        
    }
}