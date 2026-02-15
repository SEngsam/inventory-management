<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #{{ $invoice->invoice_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .container {
            margin: 0 auto;
            max-width: 800px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header h1 {
            margin: 0;
        }
        .details, .totals {
            width: 100%;
            margin-bottom: 20px;
        }
        .details th, .totals th {
            text-align: left;
            padding: 8px;
            background-color: #f0f0f0;
        }
        .details td, .totals td {
            padding: 8px;
        }
        .totals td {
            text-align: right;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h1>Invoice #{{ $invoice->invoice_number }}</h1>
        <p>{{ $invoice->customer->name }}</p>
        <p>{{ $invoice->customer->address }}</p>
        <p>{{ $invoice->customer->phone ?? 'N/A' }}</p>
    </div>

    <table class="details" border="1" cellspacing="0" cellpadding="8">
        <thead>
        <tr>
            <th>#</th>
            <th>Item</th>
            <th>Description</th>
            <th>Quantity</th>
            <th>Unit Price</th>
            <th>Total</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($invoice->items as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $item->product->name }}</td>
                <td>{{ $item->product->description ?? '—' }}</td>
                <td>{{ $item->quantity }}</td>
                <td>${{ number_format($item->unit_price, 2) }}</td>
                <td>${{ number_format($item->line_total, 2) }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <table class="totals" border="1" cellspacing="0" cellpadding="8">
        <tr>
            <th>Subtotal:</th>
            <td>${{ number_format($invoice->subtotal, 2) }}</td>
        </tr>
        <tr>
            <th>Tax:</th>
            <td>${{ number_format($invoice->tax_total, 2) }}</td>
        </tr>
        <tr>
            <th>Discount:</th>
            <td>${{ number_format($invoice->discount_total, 2) }}</td>
        </tr>
        <tr>
            <th>Total:</th>
            <td>${{ number_format($invoice->total, 2) }}</td>
        </tr>
    </table>
</div>

</body>
</html>
