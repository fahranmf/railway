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

<body class="bg-gray-50 text-gray-800 flex flex-col min-h-screen">

    <nav class="bg-white shadow-sm border-b border-gray-100 p-4 sticky top-0 z-50">
        <div class="container mx-auto flex justify-between items-center">
            <a href="{{ route('home') }}" class="flex items-center gap-2">
                <div class="bg-emerald-100 p-1.5 rounded-lg">
                    <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z">
                        </path>
                    </svg>
                </div>
                <span class="font-bold text-gray-800 text-lg">Apotek<span class="text-emerald-600">Sehat
                        24</span></span>
            </a>
            <a href="{{ route('home') }}"
                class="text-sm text-gray-500 hover:text-emerald-600 font-medium flex items-center gap-1">
                <span>←</span> Kembali Belanja
            </a>
        </div>
    </nav>

    <div class="container mx-auto px-6 py-10 flex-grow">
        <h1 class="text-2xl font-bold mb-6 flex items-center gap-2">
            🛒 Keranjang Saya
        </h1>

        @if(session('success'))
            <div class="bg-emerald-100 border border-emerald-400 text-emerald-700 px-4 py-3 rounded relative mb-4"
                role="alert">
                <strong class="font-bold">Berhasil!</strong>
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif
        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Gagal!</strong>
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif

        @if($carts->count() > 0)
            <div class="flex flex-col lg:flex-row gap-8">
                <div class="w-full lg:w-2/3">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                        <table class="w-full text-left">
                            <thead class="bg-gray-50 border-b border-gray-100 text-gray-500 text-xs uppercase">
                                <tr>
                                    <th class="px-6 py-4 font-semibold">Produk</th>
                                    <th class="px-6 py-4 font-semibold text-center">Jumlah</th>
                                    <th class="px-6 py-4 font-semibold text-right">Total</th>
                                    <th class="px-6 py-4"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($carts as $cart)
                                    <tr class="hover:bg-gray-50 transition group">
                                        <td class="px-6 py-4">
                                            <div class="font-bold text-gray-900">{{ $cart->product->name }}</div>
                                            <div class="text-sm text-gray-400">Rp
                                                {{ number_format($cart->product->price, 0, ',', '.') }} / item</div>
                                        </td>

                                        <td class="px-6 py-4">
                                            <form action="{{ route('cart.update', $cart->id) }}" method="POST"
                                                class="flex items-center justify-center bg-gray-100 rounded-full w-fit mx-auto px-2 py-1">
                                                @csrf
                                                @method('PATCH')

                                                <button type="submit" name="quantity" value="{{ $cart->quantity - 1 }}"
                                                    class="w-7 h-7 rounded-full bg-white text-gray-600 hover:bg-red-100 hover:text-red-600 flex items-center justify-center font-bold shadow-sm transition">
                                                    -
                                                </button>

                                                <input type="number" name="quantity" value="{{ $cart->quantity }}"
                                                    class="w-12 text-center bg-transparent border-none focus:ring-0 text-sm font-bold text-gray-800 p-0 mx-1"
                                                    onchange="this.form.submit()">

                                                <button type="submit" name="quantity" value="{{ $cart->quantity + 1 }}"
                                                    class="w-7 h-7 rounded-full bg-white text-emerald-600 hover:bg-emerald-600 hover:text-white flex items-center justify-center font-bold shadow-sm transition">
                                                    +
                                                </button>
                                            </form>
                                        </td>

                                        <td class="px-6 py-4 text-right font-bold text-emerald-600">
                                            Rp {{ number_format($cart->product->price * $cart->quantity, 0, ',', '.') }}
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <form action="{{ route('cart.destroy', $cart->id) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-gray-300 hover:text-red-500 transition p-2"
                                                    title="Hapus" onclick="return confirm('Yakin hapus obat ini?')">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                        </path>
                                                    </svg>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="w-full lg:w-1/3">
                    <div class="bg-white rounded-xl shadow-lg border border-emerald-100 p-6 sticky top-24">
                        <h3 class="text-lg font-bold mb-4">Ringkasan Belanja</h3>
                        <div class="flex justify-between items-center mb-2 text-gray-600">
                            <span>Total Item</span>
                            <span>{{ $carts->sum('quantity') }} pcs</span>
                        </div>

                        @php
                            $totalBayar = $carts->sum(function ($item) {
                                return $item->product->price * $item->quantity;
                            });
                        @endphp

                        <div class="flex justify-between items-center mb-6 pt-4 border-t border-dashed border-gray-200">
                            <span class="font-bold text-lg">Total Bayar</span>
                            <span class="font-bold text-2xl text-emerald-600">
                                Rp {{ number_format($totalBayar, 0, ',', '.') }}
                            </span>
                        </div>

                        <form action="{{ route('cart.checkout') }}" method="POST">
                            @csrf

                            <div class="mb-4">
                                <label for="payment_method" class="block text-sm font-medium text-gray-700 mb-2">Metode
                                    Pembayaran</label>
                                <select name="payment_method" id="payment_method"
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 p-2.5 bg-gray-50">
                                    <option value="cash" selected>Tunai (Bayar di Kasir)</option>
                                    <option value="midtrans">Online (Midtrans)</option>
                                </select>
                            </div>

                            <button type="submit"
                                class="w-full bg-emerald-600 text-white py-3 rounded-xl font-bold hover:bg-emerald-700 shadow-lg shadow-emerald-200 transition transform hover:-translate-y-1">
                                Bayar Sekarang →
                            </button>
                        </form>

                    </div>
                </div>
            </div>
        @else
            <div class="text-center py-20 bg-white rounded-xl border border-dashed border-gray-200">
                <div class="text-6xl mb-4">🛒</div>
                <h3 class="text-xl font-bold text-gray-800">Keranjang Masih Kosong</h3>
                <p class="text-gray-500 mb-6">Yuk, cari obat atau vitamin kebutuhanmu dulu.</p>
                <a href="{{ route('home') }}"
                    class="bg-emerald-600 text-white px-6 py-2 rounded-full font-bold hover:bg-emerald-700 transition">
                    Mulai Belanja
                </a>
            </div>
        @endif
    </div>

    <footer class="bg-white border-t border-gray-100 pt-16 pb-8 mt-auto">
        <div class="container mx-auto px-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-12">
                <div class="col-span-1 md:col-span-1">
                    <a href="{{ route('home') }}" class="flex items-center gap-2 mb-4">
                        <div class="bg-emerald-100 p-1.5 rounded-lg">
                            <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z">
                                </path>
                            </svg>
                        </div>
                        <span class="font-bold text-gray-800 text-lg">Apotek<span class="text-emerald-600">Sehat
                                24</span></span>
                    </a>
                    <p class="text-gray-500 text-sm leading-relaxed">
                        Melayani kebutuhan kesehatan keluarga Anda dengan sepenuh hati, 24 jam nonstop. Obat asli, harga
                        pasti.
                    </p>
                </div>
                <div>
                    <h4 class="font-bold text-gray-800 mb-4">Layanan</h4>
                    <ul class="space-y-2 text-sm text-gray-500">
                        <li><a href="#" class="hover:text-emerald-600 transition">Beli Obat</a></li>
                        <li><a href="#" class="hover:text-emerald-600 transition">Konsultasi Dokter</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-bold text-gray-800 mb-4">Bantuan</h4>
                    <ul class="space-y-2 text-sm text-gray-500">
                        <li><a href="#" class="hover:text-emerald-600 transition">Cara Belanja</a></li>
                        <li><a href="#" class="hover:text-emerald-600 transition">Informasi Pengiriman</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-bold text-gray-800 mb-4">Hubungi Kami</h4>
                    <ul class="space-y-2 text-sm text-gray-500">
                        <li class="flex items-start gap-2">
                            <span class="text-emerald-600">📍</span>
                            Jl. Kesehatan No. 123, Jakarta Selatan
                        </li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-100 pt-8 text-center">
                <p class="text-sm text-gray-400">
                    &copy; {{ date('Y') }} Apotek Sehat 24. All rights reserved.
                </p>
            </div>
        </div>
    </footer>

</body>

</html>