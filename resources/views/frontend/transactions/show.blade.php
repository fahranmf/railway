<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Transaksi #{{ $transaction->id }} - Apotek Sehat 24</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-gray-100 text-gray-800">

    <div class="min-h-screen flex flex-col justify-center items-center py-12 px-4 sm:px-6">

        <div class="w-full max-w-lg mb-6">
            <a href="{{ route('home') }}" class="text-sm text-gray-500 hover:text-emerald-600 flex items-center gap-2">
                &larr; Kembali ke Beranda
            </a>
        </div>

        <div class="bg-white shadow-xl rounded-2xl w-full max-w-lg overflow-hidden border border-gray-100">

            @if(session('success'))
            <div class="bg-emerald-500 p-6 text-center text-white">
                <div class="bg-white text-emerald-500 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4 text-3xl shadow-lg">
                    ✓
                </div>
                <h2 class="text-2xl font-bold">Pembayaran Berhasil!</h2>
                <p class="opacity-90 mt-1">Terima kasih telah berbelanja di Apotek Sehat 24</p>
            </div>
            @else
            <div class="bg-emerald-600 p-6 text-center text-white">
                <h2 class="text-xl font-bold">Bukti Transaksi</h2>
            </div>
            @endif

            <div class="p-8">
                <div class="flex justify-between items-center border-b border-gray-100 pb-4 mb-4">
                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-wide">Nomor Invoice</p>
                        <p class="font-bold text-gray-800 text-lg">#INV-{{ str_pad($transaction->id, 5, '0', STR_PAD_LEFT) }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-xs text-gray-400 uppercase tracking-wide">Tanggal</p>
                        <p class="font-semibold text-gray-800">{{ $transaction->created_at->format('d M Y, H:i') }}</p>
                    </div>
                </div>

                <div class="space-y-4 mb-6">
                    @foreach($transaction->items as $item)
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="font-bold text-gray-800">{{ $item->product_name }}</p>
                            <p class="text-xs text-gray-500">{{ $item->qty }} x Rp {{ number_format($item->price, 0, ',', '.') }}</p>

                            <div class="text-xs text-gray-400 mt-1">
                                <span class="mr-3">Kode Batch: <strong class="text-gray-700">{{ $item->batch?->batch_number ?? 'Tanpa Batch' }}</strong></span>
                                <span>Kadaluarsa: <strong class="text-gray-700">{{ $item->batch?->expired_date ? $item->batch->expired_date->format('d M Y') : '-' }}</strong></span>
                            </div>
                        </div>
                        <p class="font-semibold text-gray-800">
                            Rp {{ number_format($item->qty * $item->price, 0, ',', '.') }}
                        </p>
                    </div>
                    @endforeach
                </div>

                <div class="bg-gray-50 rounded-xl p-4 border border-gray-100">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-gray-500 text-sm">Metode Pembayaran</span>
                        <span class="font-bold text-gray-800 uppercase bg-gray-200 px-2 py-0.5 rounded text-xs">
                            {{ $transaction->payment_method }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-gray-500 text-sm">Status</span>
                        <span class="font-bold text-emerald-600 uppercase text-xs bg-emerald-100 px-2 py-0.5 rounded">
                            {{ $transaction->status }}
                        </span>
                    </div>
                    <div class="border-t border-gray-200 my-2 pt-2 flex justify-between items-center">
                        <span class="font-bold text-lg text-gray-800">Total Bayar</span>
                        <span class="font-bold text-xl text-emerald-600">
                            Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}
                        </span>
                    </div>
                </div>

                <div class="mt-8 grid grid-cols-2 gap-4">
                    <a href="{{ route('transactions.print', $transaction->id) }}" target="_blank" class="flex items-center justify-center gap-2 border border-gray-300 text-gray-700 font-bold py-3 rounded-xl hover:bg-gray-50 transition">
                        🖨️ Cetak Struk
                    </a>
                    <a href="{{ route('home') }}" class="flex items-center justify-center gap-2 bg-emerald-600 text-white font-bold py-3 rounded-xl hover:bg-emerald-700 transition shadow-lg shadow-emerald-200">
                        Belanja Lagi
                    </a>
                </div>

            </div>
        </div>
    </div>

</body>
</html>
