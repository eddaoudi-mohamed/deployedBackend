<?php

namespace App\Jobs;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class UpdateQuantProduct implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    private $status;
    private $id;
    public function __construct($status, $id)
    {
        $this->status = $status;
        $this->id = $id;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $order = Order::where("id", $this->id)->where("status", '!=', "deleted")->where("status", '!=', "paid")->firstOrFail();

        $products =  $order->products->map(function ($product) {
            return $product->pivot;
        });

        switch ($this->status) {
            case 'pending':
                foreach ($products as $key => $value) {
                    $product = Product::find($value['product_id']);
                    $unitsOnOrder = $product['unitsOnOrder'] - $value['quantity'];
                    $unitsOnOrder = $unitsOnOrder < 0 ? 0 : $unitsOnOrder;
                    $quantityInStock = $product['unitsInStock'] - $value['quantity'];
                    $product->update(['unitsOnOrder' => $unitsOnOrder, "unitsInStock" => $quantityInStock]);
                }
                break;
            case 'pendingRefunded':
                foreach ($products as $key => $value) {
                    $product = Product::find($value['product_id']);
                    $unitsOnOrder = $product['unitsOnOrder'] - $value['quantity'];
                    $unitsOnOrder = $unitsOnOrder < 0 ? 0 : $unitsOnOrder;
                    $product->update(['unitsOnOrder' => $unitsOnOrder]);
                }
                break;
            case 'paidRefunded':
                foreach ($products as $key => $value) {
                    $product = Product::find($value['product_id']);
                    $unitsOnOrder = $product['unitsOnOrder'] - $value['quantity'];
                    $unitsOnOrder = $unitsOnOrder < 0 ? 0 : $unitsOnOrder;
                    $quantityInStock = $product['unitsInStock'] + $value['quantity'];
                    $product->update(['unitsOnOrder' => $unitsOnOrder, "unitsInStock" => $quantityInStock]);
                }
                break;
        }
    }
}
