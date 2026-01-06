<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keranjang Belanja - Apotek Sehat 24</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        input[type=number]::-webkit-inner-spin-button,
        input[type=number]::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
    </style>
</head>

<script src="https://app.sandbox.midtrans.com/snap/snap.js"
        data-client-key="{{ config('services.midtrans.client_key') }}">
</script>

<div class="container mx-auto px-6 py-20">
    <div class="max-w-lg mx-auto bg-white rounded-2xl shadow-xl border border-emerald-100 p-8">
        
        <!-- HEADER -->
        <div class="text-center mb-8">
            <div class="w-14 h-14 mx-auto mb-4 rounded-full bg-emerald-100 flex items-center justify-center">
                💳
            </div>
            <h2 class="text-2xl font-bold text-gray-800">Konfirmasi Pembayaran</h2>
            <p class="text-gray-500 text-sm mt-2">
                Selesaikan pembayaran untuk memproses pesanan Anda
            </p>
        </div>

        <!-- RINGKASAN -->
        <div class="bg-gray-50 rounded-xl p-4 mb-6">
            <div class="flex justify-between text-sm text-gray-600 mb-2">
                <span>Order ID</span>
                <span class="font-medium">
                    {{ $transaction->midtrans_order_id }}
                </span>
            </div>

            <div class="flex justify-between text-sm text-gray-600 mb-2">
                <span>Total Item</span>
                <span>
                    {{ $transaction->items->sum('quantity') }} pcs
                </span>
            </div>

            <div class="flex justify-between items-center pt-3 border-t border-dashed border-gray-200">
                <span class="font-bold text-gray-800">Total Bayar</span>
                <span class="font-bold text-xl text-emerald-600">
                Rp {{ number_format((float) $transaction->total_amount, 0, ',', '.') }}

                </span>
            </div>
        </div>


        <!-- BUTTON -->
        <button id="pay-button"
            class="w-full flex items-center justify-center gap-2 bg-emerald-600 text-white py-4 rounded-xl font-bold text-lg
                   hover:bg-emerald-700 transition transform hover:-translate-y-0.5
                   shadow-lg shadow-emerald-200">
            Bayar Sekarang
        </button>

        <!-- TRUST -->
        <div class="mt-6 text-center text-xs text-gray-400 flex items-center justify-center gap-1">
            Pembayaran aman & terenkripsi oleh Midtrans
        </div>
    </div>
</div>


<script>
document.getElementById('pay-button').onclick = function () {
    snap.pay('{{ $snapToken }}', {
        onSuccess: function () {
            window.location.href = "/my-transactions/{{ $transaction->id }}";
        }
    });
};
</script>
