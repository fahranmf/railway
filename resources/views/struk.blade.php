<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk #{{ $transaction->id }}</title>
    <style>
        /* CSS Khusus Printer Thermal 58mm */
        body {
            font-family: 'Courier New', Courier, monospace; /* Font struk jadul */
            font-size: 12px;
            margin: 0;
            padding: 5px;
            max-width: 300px; /* Lebar kertas thermal */
            color: #000;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .bold { font-weight: bold; }
        .dashed-line {
            border-bottom: 1px dashed #000;
            margin: 10px 0;
            display: block;
        }

        table { width: 100%; border-collapse: collapse; }
        td { vertical-align: top; padding: 2px 0; }

        /* Sembunyikan header/footer browser saat print */
        @media print {
            @page { margin: 0; size: auto; }
            body { margin: 0; padding: 10px; }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="text-center">
        <h3 style="margin: 0;">APOTEK SEHAT 24</h3>
        <small>Jl. Raya Kampus No. 123</small><br>
        <small>Telp: 0812-3456-7890</small>
    </div>

    <div class="dashed-line"></div>

    <table>
        <tr>
            <td>No. Nota</td>
            <td class="text-right">#{{ $transaction->id }}</td>
        </tr>
        <tr>
            <td>Tanggal</td>
            <td class="text-right">{{ $transaction->created_at->format('d/m/Y H:i') }}</td>
        </tr>
        <tr>
            <td>Kasir</td>
            <td class="text-right">{{ $transaction->user->name }}</td>
        </tr>
    </table>

    <div class="dashed-line"></div>

    <table>
        @foreach($transaction->items as $item)
        <tr>
            <td colspan="2" class="bold">{{ $item->batch->product->name }}</td>
        </tr>
        <tr>
            <td>{{ $item->qty }} x {{ number_format($item->price, 0, ',', '.') }}</td>
            <td class="text-right">{{ number_format($item->qty * $item->price, 0, ',', '.') }}</td>
        </tr>
        @endforeach
    </table>

    <div class="dashed-line"></div>

    <table>
        <tr class="bold" style="font-size: 14px;">
            <td>TOTAL</td>
            <td class="text-right">Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Bayar</td>
            <td class="text-right">{{ strtoupper($transaction->payment_method) }}</td>
        </tr>
        <tr>
            <td>Status</td>
            <td class="text-right">{{ strtoupper($transaction->status) }}</td>
        </tr>
    </table>

    <div class="dashed-line"></div>

    <div class="text-center" style="margin-top: 20px;">
        Terima Kasih<br>
        Semoga Lekas Sembuh<br>
        <small><i>Barang yang dibeli tidak dapat ditukar</i></small>
    </div>

</body>
</html>
