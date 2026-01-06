<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Transaksi - {{ $transaction->id }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Courier New', monospace;
            background-color: #f5f5f5;
            padding: 20px;
        }
        .struk-container {
            max-width: 400px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            border-radius: 5px;
        }
        .struk-header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px dashed #333;
            padding-bottom: 20px;
        }
        .struk-header h1 { font-size: 20px; margin-bottom: 5px; }
        .struk-header p { font-size: 12px; color: #666; }

        .struk-info { font-size: 12px; margin-bottom: 15px; line-height: 1.5; }
        .struk-info-row { display: flex; justify-content: space-between; margin-bottom: 5px; }

        .struk-items {
            border-top: 2px dashed #333;
            border-bottom: 2px dashed #333;
            padding: 15px 0;
            margin-bottom: 20px;
        }
        .item-row { display: flex; justify-content: space-between; font-size: 12px; margin-bottom: 8px; }
        .item-name { flex: 1; font-weight: bold; }
        .item-qty { width: 40px; text-align: center; }
        .item-price { width: 80px; text-align: right; }

        .struk-total {
            display: flex; justify-content: space-between;
            font-weight: bold; font-size: 14px; margin-bottom: 20px;
        }

        .payment-status {
            padding: 8px; text-align: center; font-weight: bold;
            margin-top: 10px; border-radius: 3px; font-size: 12px;
        }
        .payment-pending { background-color: #fff3cd; color: #856404; }
        .payment-paid { background-color: #d4edda; color: #155724; }
        .payment-cancel { background-color: #f8d7da; color: #721c24; }

        .struk-footer {
            text-align: center; font-size: 11px; color: #666;
            margin-top: 20px; border-top: 1px dashed #ddd; padding-top: 10px;
        }

        .print-button {
            text-align: center; margin-top: 20px; padding-top: 15px; border-top: 1px solid #ddd;
        }
        .btn {
            padding: 10px 15px; margin: 0 5px; border: none; border-radius: 4px;
            cursor: pointer; font-size: 12px; font-weight: bold; text-decoration: none; display: inline-block;
        }
        .btn-print { background-color: #007bff; color: white; }
        .btn-back { background-color: #6c757d; color: white; }

        /* Sembunyikan elemen web saat print (Hanya cetak struk) */
        @media print {
            body { background: white; padding: 0; }
            .struk-container { box-shadow: none; max-width: 100%; margin: 0; padding: 0; }
            .no-print, .btn, .print-button { display: none !important; }
            .payment-status { border: 1px solid #000; color: #000; background: none; }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="struk-container">
        <div class="struk-header">
            <h1>APOTEK SEHAT 24</h1>
            <p>Jl. Raya Kampus No. 123</p>
            <p>Telp: 0812-3456-7890</p>
        </div>

        <div class="struk-info">
            <div class="struk-info-row">
                <span>Nota: #{{ $transaction->id }}</span>
                <span>{{ $transaction->created_at->format('d/m/y H:i') }}</span>
            </div>
            <div class="struk-info-row">
                <span>Pelanggan: {{ $transaction->user->name ?? 'Umum' }}</span>
                <span>{{ strtoupper($transaction->payment_method) }}</span>
            </div>
        </div>

        <div class="struk-items">
            @foreach($transaction->items as $item)
                <div class="item-row">
                    <div class="item-name">
                        {{ $item->product->name ?? $item->batch->product->name ?? 'Item Tidak Dikenal' }}
                    </div>

                    <div class="item-qty">x{{ $item->qty ?? $item->quantity }}</div>

                    <div class="item-price">
                        {{ number_format(($item->qty ?? $item->quantity) * $item->price, 0, ',', '.') }}
                    </div>
                </div>
            @endforeach
        </div>

        <div class="struk-total">
            <div>TOTAL BAYAR</div>
            <div>Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</div>
        </div>

        <div class="payment-status payment-{{ $transaction->status }}">
            STATUS: {{ strtoupper($transaction->status) }}
        </div>

        <div class="struk-footer">
            <p>Terima Kasih & Semoga Lekas Sembuh</p>
            <p><i>Barang yang dibeli tidak dapat ditukar</i></p>
        </div>

        <div class="print-button no-print">
            <button class="btn btn-print" onclick="window.print()">🖨️ Cetak</button>
            <a href="{{ route('home') }}" class="btn btn-back">← Kembali</a>
        </div>
    </div>
</body>
</html>
