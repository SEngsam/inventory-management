<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class InvoiceService
{
    public function __construct(
        private readonly InvoiceNumberService $numberService
    ) {}

    /**
     * @param array $invoiceData  ['customer_id','issued_at','status','subtotal','tax_total','discount_total','total', ...]
     * @param array $lines        each: ['product_id','quantity','unit_price','tax_value','tax_type','discount','line_tax','line_total']
     */
    public function createFromSale(array $invoiceData, array $lines): Invoice
    {
        return DB::transaction(function () use ($invoiceData, $lines) {

            if (empty($lines)) {
                throw ValidationException::withMessages([
                    'items' => 'Invoice must have at least one item.',
                ]);
            }

            $subtotal = 0.0;
            $taxTotal = 0.0;
            $discountTotal = 0.0;
            $grandTotal = 0.0;

            foreach ($lines as $l) {
                $qty = (int)($l['quantity'] ?? 0);
                $unit = (float)($l['unit_price'] ?? 0);
                $disc = (float)($l['discount'] ?? 0);
                $lineTax = (float)($l['line_tax'] ?? 0);

                $lineSub = ($unit * $qty) - $disc;
                if ($lineSub < 0) $lineSub = 0;

                $lineTotal = (float)($l['line_total'] ?? ($lineSub + $lineTax));

                $subtotal += $lineSub;
                $taxTotal += $lineTax;
                $discountTotal += $disc;
                $grandTotal += $lineTotal;
            }

            $invoiceNumber = $this->numberService->next();

            $invoice = Invoice::create([
                'invoice_number' => $invoiceNumber,
                'status'         => $invoiceData['status'] ?? 'issued',

                'customer_id'    => $invoiceData['customer_id'] ?? null,
                'user_id'        => $invoiceData['user_id'] ?? Auth::id(),

                'issued_at'      => $invoiceData['issued_at'] ?? now(),

                'subtotal'       => round($subtotal, 2),
                'tax_total'      => round($taxTotal, 2),
                'discount_total' => round($discountTotal, 2),
                'total'          => round($grandTotal, 2),
            ]);

            foreach ($lines as $l) {
                $productId = (int)$l['product_id'];
                $qty = (int)$l['quantity'];

                $updated = DB::table('products')
                    ->where('id', $productId)
                    ->where('stock_quantity', '>=', $qty)
                    ->decrement('stock_quantity', $qty);

                if ($updated === 0) {
                    throw ValidationException::withMessages([
                        'items' => "Insufficient stock for product ID {$productId}.",
                    ]);
                }

                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'product_id' => $productId,
                    'quantity'   => $qty,

                    'unit_price' => round((float)$l['unit_price'], 2),

                    'tax_value'  => $l['tax_value'] !== null ? round((float)$l['tax_value'], 2) : null,
                    'tax_type'   => $l['tax_type'] ?? 'percent',

                    'discount'   => round((float)($l['discount'] ?? 0), 2),

                    'line_tax'   => round((float)($l['line_tax'] ?? 0), 2),
                    'line_total' => round((float)$l['line_total'], 2),
                ]);
            }

            return $invoice;

        }, 3);
    }
}
