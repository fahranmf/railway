<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Belanja - Apotek Sehat 24</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-gray-50 text-gray-800">

    <nav class="bg-white shadow-sm border-b border-gray-100 p-4 sticky top-0 z-50">
        <div class="container mx-auto flex justify-between items-center">
            <a href="{{ route('home') }}" class="flex items-center gap-2">
                <div class="bg-emerald-100 p-1.5 rounded-lg">
                    <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
                </div>
                <span class="font-bold text-gray-800 text-lg">Apotek<span class="text-emerald-600">Sehat 24</span></span>
            </a>
            <a href="{{ route('home') }}" class="text-sm text-gray-500 hover:text-emerald-600 font-medium flex items-center gap-1">
                <span>←</span> Kembali Belanja
            </a>
        </div>
    </nav>

    <div class="container mx-auto px-6 py-10">
        <h1 class="text-2xl font-bold mb-6 flex items-center gap-2">
            📜 Riwayat Belanja Anda
        </h1>

        @if(session('success'))
            <div class="bg-emerald-100 border border-emerald-400 text-emerald-700 px-4 py-3 rounded relative mb-6">
                <strong class="font-bold">Berhasil!</strong> {{ session('success') }}
            </div>
        @endif

        @if(isset($transactions) && $transactions->count() > 0)
            <div class="grid grid-cols-1 gap-6">
                @foreach($transactions as $transaction)

                {{--
                    🔥 FORMULA HITUNG TOTAL (HYBRID / ANTI-GAGAL) 🔥
                    Kita cek semua kemungkinan tempat penyimpanan data harga & jumlah
                --}}
                @php
                    $calculatedTotal = $transaction->items->sum(function($item){
                        // 1. Cek Jumlah (Quantity) di berbagai tempat
                        $qty = $item->quantity
                                ?? $item->qty
                                ?? $item->pivot->quantity
                                ?? $item->pivot->qty
                                ?? 0;

                        // 2. Cek Harga (Price) di berbagai tempat
                        $price = $item->price
                                 ?? $item->product_price
                                 ?? $item->pivot->price
                                 ?? $item->pivot->total_price
                                 ?? 0;

                        return $qty * $price;
                    });
                @endphp

                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition duration-300">
                    <div class="bg-gray-50 px-6 py-4 border-b border-gray-100 flex flex-col md:flex-row justify-between md:items-center gap-4">
                        <div class="flex items-center gap-4">
                            <div class="bg-emerald-100 p-2 rounded-full">
                                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <div>
                                <div class="text-xs font-bold text-gray-400 uppercase tracking-wider">No. Invoice</div>
                                <div class="font-bold text-gray-800 text-lg">
                                    {{ $transaction->invoice_code ?? '#TRX-'.$transaction->id }}
                                </div>
                            </div>
                        </div>
                        <div class="text-sm text-gray-700 font-medium">
                            {{ $transaction->created_at->format('d F Y, H:i') }} WIB
                        </div>
                        <div>
                            <span class="px-4 py-1.5 rounded-full text-xs font-bold uppercase tracking-wide bg-emerald-100 text-emerald-700 border border-emerald-200">
                                {{ $transaction->status }}
                            </span>
                        </div>
                    </div>

                    <div class="px-6 py-5">
                        <h4 class="text-sm font-bold text-gray-500 uppercase mb-3 border-b border-gray-100 pb-2">Rincian Barang</h4>
                        <ul class="space-y-3">
                            @foreach($transaction->items as $item)
                                @php
                                    // LOGIKA PENCARIAN DATA ITEM

                                    // 1. Cari Nama Barang
                                    // Cek $item->product->name (Relasi HasMany) ATAU $item->name (Relasi BelongsToMany)
                                    $name = $item->product->name
                                            ?? $item->name
                                            ?? 'Produk Obat (ID: '. ($item->product_id ?? $item->id) .')';

                                    // 2. Cari Jumlah
                                    $qty = $item->quantity
                                           ?? $item->qty
                                           ?? $item->pivot->quantity
                                           ?? $item->pivot->qty
                                           ?? 0;

                                    // 3. Cari Harga
                                    $price = $item->price
                                             ?? $item->product_price
                                             ?? $item->pivot->price
                                             ?? 0;

                                    $subtotal = $price * $qty;
                                @endphp

                                <li class="flex justify-between items-center text-sm group">
                                    <div class="flex items-center gap-3">
                                        <div class="w-2 h-2 rounded-full bg-emerald-400"></div>
                                        <span class="font-medium text-gray-700">
                                            {{ $name }}
                                        </span>
                                        <span class="text-xs text-gray-400 bg-gray-100 px-2 py-0.5 rounded-md">
                                            x {{ $qty }}
                                        </span>
                                    </div>
                                    <span class="font-medium text-gray-600">
                                        Rp {{ number_format($subtotal, 0, ',', '.') }}
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <div class="bg-emerald-50/50 px-6 py-4 flex justify-between items-center border-t border-emerald-100">
                        <div>
                            <span class="block text-xs text-gray-500 font-medium">Total Tagihan</span>
                            <span class="font-bold text-xl text-emerald-700">
                                Rp {{ number_format($calculatedTotal, 0, ',', '.') }}
                            </span>
                        </div>

                        <a href="{{ route('transactions.show', $transaction->id) }}"
                           class="inline-flex items-center gap-2 bg-white border border-emerald-200 text-emerald-600 px-5 py-2.5 rounded-lg font-bold text-sm hover:bg-emerald-600 hover:text-white transition shadow-sm hover:shadow-md">
                            Lihat Struk
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-20 bg-white rounded-xl border border-dashed border-gray-300 shadow-sm">
                <h3 class="text-xl font-bold text-gray-800 mb-2">Belum Ada Transaksi</h3>
                <a href="{{ route('home') }}" class="mt-4 inline-block bg-emerald-600 text-white px-6 py-2 rounded-lg font-bold">Mulai Belanja</a>
            </div>
        @endif
    </div>

    <div class="text-center py-8 text-sm text-gray-400">
        &copy; {{ date('Y') }} Apotek Sehat 24.
    </div>

</body>
</html>
