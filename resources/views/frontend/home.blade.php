<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apotek Sehat - Solusi Kesehatan Keluarga</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            scroll-behavior: smooth;
        }

        .fade-up {
            animation: fadeUp 0.5s ease-out forwards;
        }

        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Sembunyikan scrollbar tapi tetap bisa di-scroll */
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
</head>

<body class="min-h-screen flex flex-col bg-gray-50 text-gray-800 antialiased">


    <nav class="bg-white shadow-sm sticky top-0 z-40 border-b border-gray-100">
        <div class="container mx-auto px-6 py-4 flex justify-between items-center">
            <a href="{{ route('home') }}" class="flex items-center gap-2 group">
                <div class="bg-emerald-100 p-2 rounded-lg group-hover:bg-emerald-200 transition">
                    <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z">
                        </path>
                    </svg>
                </div>
                <span class="text-xl font-bold text-gray-800 tracking-tight">Apotek<span class="text-emerald-600">Sehat
                        24</span></span>
            </a>

            <div class="flex items-center gap-4">
                @auth
                    <div class="hidden md:flex flex-col text-right mr-2">
                        <span class="text-xs text-gray-500">Hai,</span>
                        <span class="text-sm font-bold text-gray-800">{{ Auth::user()->name }}</span>
                    </div>

                    <a href="{{ route('transactions.index') }}"
                        class="relative p-2 text-gray-400 hover:text-emerald-600 transition group" title="Riwayat Belanja">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01">
                            </path>
                        </svg>
                        <span
                            class="absolute top-10 right-0 bg-gray-800 text-white text-[10px] px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition whitespace-nowrap pointer-events-none">
                            Riwayat
                        </span>
                    </a>

                    <a href="{{ route('cart.index') }}" class="relative p-2 text-gray-400 hover:text-emerald-600 transition"
                        title="Keranjang">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                        </svg>
                        @if(isset($globalCartCount) && $globalCartCount > 0)
                            <span
                                class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white transform translate-x-1/4 -translate-y-1/4 bg-red-500 rounded-full border-2 border-white">
                                {{ $globalCartCount }}
                            </span>
                        @endif
                    </a>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="bg-gray-100 text-gray-600 px-4 py-2 rounded-lg text-sm font-semibold hover:bg-red-50 hover:text-red-600 transition">Keluar</button>
                    </form>
                @else
                    <a href="{{ route('login') }}"
                        class="text-gray-600 hover:text-emerald-600 font-medium text-sm">Masuk</a>
                    <a href="{{ route('register') }}"
                        class="bg-emerald-600 text-white px-5 py-2.5 rounded-full text-sm font-semibold shadow-lg hover:bg-emerald-700 transition">Daftar</a>
                @endauth
            </div>
        </div>
    </nav>

    <main class="flex-grow pb-24">

        @if(session('success'))
            <div class="container mx-auto px-6 mt-4">
                <div
                    class="bg-emerald-100 border-l-4 border-emerald-500 text-emerald-700 p-4 rounded shadow-sm flex justify-between items-center">
                    <span>{{ session('success') }}</span>
                    <span>✅</span>
                </div>
            </div>
        @endif
        @if(session('error'))
            <div class="container mx-auto px-6 mt-4">
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded shadow-sm">
                    <span class="font-bold">Gagal: </span> {{ session('error') }}
                </div>
            </div>
        @endif

        <div class="relative bg-emerald-50 overflow-hidden">
            <div class="container mx-auto px-6 py-12 md:py-20 relative z-10 flex flex-col md:flex-row items-center">
                <div class="w-full md:w-1/2 mb-10 md:mb-0 text-center md:text-left">
                    <h1 class="text-4xl md:text-5xl font-bold text-gray-900 leading-tight mb-6">
                        Solusi Obat <br> <span class="text-emerald-600">Terlengkap & Cepat.</span>
                    </h1>
                    <p class="text-gray-600 text-lg mb-8 max-w-lg mx-auto md:mx-0">
                        Cari obat resep dokter atau kebutuhan harian? Semua ada di sini.
                    </p>
                    <a href="#katalog"
                        class="bg-emerald-600 text-white px-8 py-3 rounded-full font-bold shadow-lg hover:bg-emerald-700 transition">
                        Mulai Belanja
                    </a>
                </div>
                <div class="w-full md:w-1/2 flex justify-center">
                    <div class="text-[10rem] animate-bounce">💊</div>
                </div>
            </div>
        </div>

        <div id="katalog" class="container mx-auto px-6 py-10">
            <div class="flex flex-col md:flex-row justify-between items-end mb-6 gap-4">
                <div>
                    <span class="text-emerald-600 font-bold tracking-wider uppercase text-sm">Katalog</span>
                    <h2 class="text-3xl font-bold text-gray-900 mt-1">Daftar Obat</h2>
                </div>

                <form action="{{ route('home') }}" method="GET" class="w-full md:w-1/3 relative">
                    @if(request('category'))
                        <input type="hidden" name="category" value="{{ request('category') }}">
                    @endif
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari obat..."
                        class="w-full pl-10 pr-4 py-2.5 rounded-full border border-gray-300 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition shadow-sm">
                    <svg class="w-5 h-5 text-gray-400 absolute left-3 top-3" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </form>
            </div>

            @if(isset($categories) && count($categories) > 0)
                <div class="flex gap-2 overflow-x-auto pb-6 mb-2 no-scrollbar">
                    <a href="{{ route('home', ['search' => request('search')]) }}"
                        class="px-5 py-2 rounded-full text-sm font-semibold whitespace-nowrap transition border {{ !request('category') ? 'bg-emerald-600 text-white border-emerald-600' : 'bg-white text-gray-600 border-gray-200 hover:border-emerald-600 hover:text-emerald-600' }}">
                        Semua
                    </a>
                    @foreach($categories as $cat)
                        <a href="{{ route('home', ['category' => $cat->id, 'search' => request('search')]) }}"
                            class="px-5 py-2 rounded-full text-sm font-semibold whitespace-nowrap transition border {{ request('category') == $cat->id ? 'bg-emerald-600 text-white border-emerald-600' : 'bg-white text-gray-600 border-gray-200 hover:border-emerald-600 hover:text-emerald-600' }}">
                            {{ $cat->name }}
                        </a>
                    @endforeach
                </div>
            @endif

            @if($products->count() > 0)
                <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-6">
                    @foreach($products as $product)
                        <div id="product-{{ $product->id }}"
                            class="bg-white rounded-2xl shadow-sm border border-gray-100 hover:shadow-lg hover:border-emerald-100 transition duration-300 flex flex-col h-full group scroll-mt-28">

                            <div
                                class="relative h-48 bg-gray-50 rounded-t-2xl overflow-hidden flex items-center justify-center group-hover:bg-emerald-50 transition">
                                @if($product->total_stock > 0)
                                    <span
                                        class="absolute top-3 left-3 bg-emerald-100 text-emerald-700 text-[10px] font-bold px-2 py-1 rounded-full">
                                        Stok: {{ $product->total_stock }}
                                    </span>
                                @else
                                    <span
                                        class="absolute top-3 left-3 bg-red-100 text-red-700 text-[10px] font-bold px-2 py-1 rounded-full">
                                        Habis
                                    </span>
                                @endif
                                <span class="text-6xl grayscale group-hover:grayscale-0 transition duration-300">💊</span>
                            </div>

                            <div class="p-4 flex flex-col flex-grow">
                                <div class="mb-1 text-xs text-gray-400 uppercase font-semibold tracking-wider">
                                    {{ $product->category->name ?? 'Umum' }}
                                </div>
                                <h3
                                    class="text-gray-900 font-bold text-base leading-tight mb-2 group-hover:text-emerald-600 transition">
                                    {{ $product->name }}
                                </h3>

                                <div class="flex-grow"></div>

                                <div class="flex items-center justify-between mt-4">
                                    <div class="flex flex-col">
                                        <span class="text-xs text-gray-400">Harga</span>
                                        <span class="text-emerald-600 font-bold text-lg">
                                            Rp {{ number_format($product->price, 0, ',', '.') }}
                                        </span>
                                    </div>

                                    @auth
                                        @if($product->total_stock > 0)
                                            <form action="{{ route('cart.add', $product->id) }}" method="POST">
                                                @csrf
                                                <button type="submit"
                                                    class="w-10 h-10 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center hover:bg-emerald-600 hover:text-white transition shadow-sm"
                                                    title="Tambah">
                                                    <span class="text-xl font-bold">+</span>
                                                </button>
                                            </form>
                                        @else
                                            <button disabled
                                                class="w-10 h-10 rounded-full bg-gray-100 text-gray-300 flex items-center justify-center cursor-not-allowed">
                                                <span class="text-xl font-bold">x</span>
                                            </button>
                                        @endif
                                    @else
                                        <a href="{{ route('login') }}"
                                            class="w-10 h-10 rounded-full bg-gray-50 text-gray-400 flex items-center justify-center hover:bg-emerald-600 hover:text-white transition shadow-sm">
                                            <span class="text-xl font-bold">+</span>
                                        </a>
                                    @endauth
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-20 bg-gray-50 rounded-xl border border-dashed border-gray-200">
                    <div class="text-6xl mb-4">🔍</div>
                    <h3 class="text-xl font-bold text-gray-800">Tidak ada produk ditemukan</h3>
                    <p class="text-gray-500 mt-2">Coba kata kunci lain atau pilih kategori "Semua".</p>
                    <a href="{{ route('home') }}" class="inline-block mt-4 text-emerald-600 font-bold hover:underline">Reset
                        Filter</a>
                </div>
            @endif
        </div>

        @if(auth()->check() && isset($globalCartCount) && $globalCartCount > 0)
            <div class="fixed bottom-6 inset-x-0 z-50 flex justify-center px-4 fade-up">
                <a href="{{ route('cart.index') }}"
                    class="bg-gray-900 text-white rounded-2xl shadow-2xl py-3 px-6 w-full max-w-lg flex justify-between items-center hover:bg-gray-800 transition transform hover:-translate-y-1 cursor-pointer ring-4 ring-emerald-50 border border-gray-700">
                    <div class="flex flex-col">
                        <span class="text-xs text-emerald-400 font-medium mb-0.5">{{ $globalCartCount }} Barang</span>
                        <span class="text-lg font-bold text-white">Rp
                            {{ number_format($globalTotal ?? 0, 0, ',', '.') }}</span>
                    </div>
                    <div
                        class="flex items-center gap-2 font-bold text-sm bg-emerald-600 text-white py-2 px-4 rounded-xl shadow-lg">
                        Lihat Keranjang <span class="ml-1">→</span>
                    </div>
                </a>
            </div>
        @endif
    </main>

    <footer class="bg-gray-900 text-gray-300 pt-16 pb-32 md:pb-12 border-t border-gray-800">
        <div class="container mx-auto px-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
                <div class="col-span-1 md:col-span-1">
                    <div class="flex items-center gap-2 mb-4">
                        <div class="bg-emerald-500 p-1.5 rounded-lg">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z">
                                </path>
                            </svg>
                        </div>
                        <span class="font-bold text-white text-xl">Apotek<span class="text-emerald-500">Sehat
                                24</span></span>
                    </div>
                    <p class="text-sm leading-relaxed text-gray-400">
                        Solusi kesehatan keluarga terpercaya. Obat asli, konsultasi mudah, dan pengiriman cepat 24 jam
                        non-stop.
                    </p>
                </div>

                <div>
                    <h4 class="font-bold text-white mb-4 uppercase text-sm tracking-wider">Layanan</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="#" class="hover:text-emerald-400 transition">Beli Obat</a></li>
                        <li><a href="#" class="hover:text-emerald-400 transition">Upload Resep</a></li>
                        <li><a href="#" class="hover:text-emerald-400 transition">Konsultasi Dokter</a></li>
                        <li><a href="#" class="hover:text-emerald-400 transition">Booking Vaksin</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="font-bold text-white mb-4 uppercase text-sm tracking-wider">Informasi</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="#" class="hover:text-emerald-400 transition">Tentang Kami</a></li>
                        <li><a href="#" class="hover:text-emerald-400 transition">Kebijakan Privasi</a></li>
                        <li><a href="#" class="hover:text-emerald-400 transition">Syarat & Ketentuan</a></li>
                        <li><a href="#" class="hover:text-emerald-400 transition">FAQ</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="font-bold text-white mb-4 uppercase text-sm tracking-wider">Hubungi Kami</h4>
                    <ul class="space-y-3 text-sm">
                        <li class="flex items-start gap-3">
                            <span class="text-emerald-500 mt-0.5">📍</span>
                            <span>Jl. Jendral Sudirman No. 88,<br>Jakarta Selatan, 12190</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <span class="text-emerald-500">📞</span>
                            <span>(021) 789-0011</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <span class="text-emerald-500">💬</span>
                            <span>0812-3456-7890 (WhatsApp)</span>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="border-t border-gray-800 pt-8 flex flex-col md:flex-row justify-between items-center">
                <p class="text-sm text-gray-500 text-center md:text-left">
                    &copy; {{ date('Y') }} Apotek Sehat 24. Hak Cipta Dilindungi.
                </p>
                <div class="flex gap-4 mt-4 md:mt-0">
                    <a href="#" class="text-gray-500 hover:text-white transition">Instagram</a>
                    <a href="#" class="text-gray-500 hover:text-white transition">Facebook</a>
                    <a href="#" class="text-gray-500 hover:text-white transition">Twitter</a>
                </div>
            </div>
        </div>
    </footer>
</body>

</html>