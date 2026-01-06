<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Transaksi - {{ $transaction->id }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

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
            margin-bottom: 30px;
            border-bottom: 2px dashed #333;
            padding-bottom: 20px;
        }

        .struk-header h1 {
            font-size: 24px;
            margin-bottom: 5px;
        }

        .struk-header p {
            font-size: 12px;
            color: #666;
        }

        .struk-info {
            font-size: 12px;
            margin-bottom: 20px;
            line-height: 1.8;
        }

        .struk-info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }

        .struk-info label {
            font-weight: bold;
            min-width: 100px;
        }

        .struk-items {
            border-top: 2px dashed #333;
            border-bottom: 2px dashed #333;
            padding: 15px 0;
            margin-bottom: 20px;
        }

        .item-row {
            display: flex;
            justify-content: space-between;
            font-size: 12px;
            margin-bottom: 8px;
            line-height: 1.4;
        }

        .item-name {
            flex: 1;
        }

        .item-qty {
            width: 40px;
            text-align: center;
        }

        .item-price {
            width: 80px;
            text-align: right;
        }

        .item-separator {
            border-top: 1px dashed #999;
            margin: 8px 0;
            font-size: 10px;
            color: #999;
        }

        .struk-total {
            display: flex;
            justify-content: space-between;
            font-weight: bold;
            font-size: 14px;
            margin-bottom: 20px;
        }

        .struk-total-label {
            min-width: 200px;
        }

        .struk-total-amount {
            text-align: right;
        }

        .struk-footer {
            text-align: center;
            font-size: 12px;
            color: #666;
            border-top: 2px dashed #333;
            padding-top: 15px;
            margin-top: 20px;
        }

        .struk-footer p {
            margin-bottom: 5px;
        }

        .payment-status {
            padding: 10px;
            text-align: center;
            font-weight: bold;
            margin-top: 15px;
            border-radius: 3px;
        }

        .payment-pending {
            background-color: #fff3cd;
            color: #856404;
        }

        .payment-paid {
            background-color: #d4edda;
            color: #155724;
        }

        .payment-cancel {
            background-color: #f8d7da;
            color: #721c24;
        }

        @media print {
            body {
                background: white;
                padding: 0;
            }

            .struk-container {
                box-shadow: none;
                max-width: 80mm;
                margin: 0;
            }

            .no-print {
                display: none;
            }
        }

        .print-button {
            text-align: center;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
        }

        .btn {
            padding: 10px 20px;
            margin: 0 5px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            font-weight: bold;
        }

        .btn-print {
            background-color: #007bff;
            color: white;
        }

        .btn-print:hover {
            background-color: #0056b3;
        }

        .btn-back {
            background-color: #6c757d;
            color: white;
        }

        .btn-back:hover {
            background-color: #545b62;
        }
    </style>
</head>
<body>
    <div class="struk-container">
        <!-- HEADER -->
        <div class="struk-header">
            <h1>🏥 APOTEK SEHAT 24</h1>
            <p>Jalan Kesehatan No. 123</p>
            <p>Telp: (021) 1234-5678</p>
        </div>

        <!-- INFO TRANSAKSI -->
        <div class="struk-info">
            <div class="struk-info-row">
                <label>No. Struk</label>
                <span>#{{ $transaction->id }}</span>
            </div>
            <div class="struk-info-row">
                <label>Tanggal</label>
                <span>{{ $transaction->created_at->format('d/m/Y H:i') }}</span>
            </div>
            <div class="struk-info-row">
                <label>Kasir</label>
                <span>{{ $transaction->user->name }}</span>
            </div>
            <div class="struk-info-row">
                <label>Metode</label>
                <span>
                    @switch($transaction->payment_method)
                        @case('cash')
                            Tunai
                            @break
                        @case('transfer')
                            Transfer Bank
                            @break
                        @case('qris')
                            QRIS
                            @break
                        @default
                            {{ $transaction->payment_method }}
                    @endswitch
                </span>
            </div>
        </div>

        <!-- ITEM TRANSAKSI -->
        <div class="struk-items">
            <div class="item-row" style="font-weight: bold; margin-bottom: 8px;">
                <div class="item-name">Obat</div>
                <div class="item-qty">Qty</div>
                <div class="item-price">Subtotal</div>
            </div>
            <div class="item-separator"></div>

            @forelse($transaction->items as $item)
                <div class="item-row">
                    <div class="item-name">{{ $item->product->name ?? 'N/A' }}</div>
                    <div class="item-qty">{{ $item->qty }}</div>
                    <div class="item-price">Rp {{ number_format($item->qty * $item->price, 0, ',', '.') }}</div>
                </div>
            @empty
                <div class="item-row" style="text-align: center; color: #999;">
                    <span>Tidak ada item</span>
                </div>
            @endforelse
        </div>

        <!-- TOTAL -->
        <div class="struk-total">
            <div class="struk-total-label">TOTAL</div>
            <div class="struk-total-amount">Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</div>
        </div>

        <!-- STATUS PEMBAYARAN -->
        <div class="payment-status payment-{{ $transaction->status }}">
            @switch($transaction->status)
                @case('pending')
                    ⏳ MENUNGGU PEMBAYARAN
                    @break
                @case('paid')
                    ✓ LUNAS
                    @break
                @case('cancel')
                    ✗ DIBATALKAN
                    @break
                @default
                    {{ $transaction->status }}
            @endswitch
        </div>

        <!-- FOOTER -->
        <div class="struk-footer">
            <p>Terima kasih telah berbelanja</p>
            <p>Semoga lekas sembuh!</p>
            <p style="margin-top: 10px; font-size: 10px; color: #999;">
                Dicetak: {{ now()->format('d/m/Y H:i:s') }}
            </p>
        </div>

        <!-- TOMBOL PRINT & BACK -->
        <div class="print-button no-print">
            <button class="btn btn-print" onclick="window.print()">🖨️ Cetak Struk</button>
            <a href="{{ route('filament.admin.resources.transactions.index') }}" class="btn btn-back">← Kembali</a>
        </div>
    </div>
</body>
</html>
