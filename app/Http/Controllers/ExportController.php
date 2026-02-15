<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\Customer;
use App\Models\Product;
use Barryvdh\DomPDF\Facade\Pdf;
use Symfony\Component\HttpFoundation\Response;

class ExportController extends Controller
{
    public function __invoke(string $format, string $type, int $id)
    {
        $model = $this->resolveModel($type, $id);

        if (!$model) {
            abort(404);
        }

        return match ($format) {
            'pdf'   => $this->exportPdf($type, $model),
            'excel' => $this->exportExcel($type, $model),
            default => abort(400, 'Invalid export format'),
        };
    }

    private function resolveModel(string $type, int $id)
    {
        return match ($type) {
            'invoice'  => Invoice::with(['customer', 'items.product'])->findOrFail($id),
            'customer' => Customer::findOrFail($id),
            'product'  => Product::findOrFail($id),
            default    => null,
        };
    }

    public function exportPdf(string $type, $model): Response
    {
        $view = "exports.pdf.$type";  

        $pdf = Pdf::loadView($view, ['invoice' => $model]) 
            ->setPaper('a4');

        return $pdf->stream("{$type}-{$model->id}.pdf");
    }

    private function exportExcel(string $type, $model)
    {
        return response()->json([
            'message' => 'Excel export not implemented yet.'
        ]);
    }
}
