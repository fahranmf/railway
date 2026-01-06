<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Area Pelanggan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-gray-100">
                <div class="p-8 text-gray-900">

                    <div class="flex flex-col md:flex-row items-center gap-8">
                        <div class="flex-shrink-0">
                             <div class="w-24 h-24 bg-emerald-50 rounded-full flex items-center justify-center">
                                <span class="text-5xl">👋</span>
                             </div>
                        </div>

                        <div class="flex-grow text-center md:text-left">
                            <h3 class="text-3xl font-bold text-gray-900 mb-2">Halo, {{ Auth::user()->name }}!</h3>
                            <p class="text-gray-600 text-lg mb-6">Senang bertemu Anda kembali. Akun Anda aktif dan siap digunakan untuk berbelanja kebutuhan kesehatan.</p>

                            <div class="flex flex-col sm:flex-row gap-4 justify-center md:justify-start">
                                <a href="{{ route('home') }}" class="bg-emerald-600 text-white px-8 py-3 rounded-full font-bold shadow-lg shadow-emerald-200 hover:bg-emerald-700 hover:shadow-emerald-300 transition transform hover:-translate-y-0.5 inline-flex items-center justify-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                                    Mulai Belanja
                                </a>

                                <a href="{{ route('profile.edit') }}" class="bg-white text-gray-700 border border-gray-300 px-6 py-3 rounded-full font-semibold hover:bg-gray-50 transition inline-flex items-center justify-center">
                                    Edit Profil
                                </a>
                            </div>
                        </div>
                    </div>

                    <hr class="my-8 border-gray-100">

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="bg-gray-50 p-4 rounded-xl border border-gray-100">
                            <span class="text-xs text-gray-400 font-bold uppercase tracking-wider">Email Terdaftar</span>
                            <div class="font-medium text-gray-800 mt-1">{{ Auth::user()->email }}</div>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-xl border border-gray-100">
                            <span class="text-xs text-gray-400 font-bold uppercase tracking-wider">No. Handphone</span>
                            <div class="font-medium text-gray-800 mt-1">{{ Auth::user()->phone ?? '-' }}</div>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-xl border border-gray-100">
                            <span class="text-xs text-gray-400 font-bold uppercase tracking-wider">Kota Domisili</span>
                            <div class="font-medium text-gray-800 mt-1">{{ Auth::user()->city ?? '-' }}</div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
