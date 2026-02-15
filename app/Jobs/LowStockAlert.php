<?php

namespace App\Jobs;

use App\Mail\LowStockMail;
use App\Models\Product;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Foundation\Queue\Queueable;


class LowStockAlert implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        $lowStockProducts = Product::where('stock_quantity', '<=', 'threshold_stock')->get();

        foreach ($lowStockProducts as $product) {
             
            Mail::to('admin@example.com')->send(new LowStockMail($product));
        }
    }
}
