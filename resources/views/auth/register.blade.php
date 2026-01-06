<x-guest-layout>

    <h2 class="text-2xl font-bold text-center text-gray-800 mb-8">
        Daftar Akun Baru
    </h2>

    <form method="POST" action="{{ route('register') }}" class="space-y-6">
        @csrf

        <div>
            <label for="name" class="block text-sm font-semibold text-gray-700 mb-1">
                Nama Lengkap
            </label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name"
                class="w-full rounded-xl border-gray-300 px-4 py-3 shadow-sm
                   focus:border-emerald-500 focus:ring-emerald-500" placeholder="Nama sesuai KTP" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div>
            <label for="email" class="block text-sm font-semibold text-gray-700 mb-1">
                Email
            </label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username"
                class="w-full rounded-xl border-gray-300 px-4 py-3 shadow-sm
                   focus:border-emerald-500 focus:ring-emerald-500" placeholder="email@example.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <label for="phone" class="block text-sm font-semibold text-gray-700 mb-1">
                No. Handphone / WA
            </label>
            <input id="phone" type="text" name="phone" value="{{ old('phone') }}" required class="w-full rounded-xl border-gray-300 px-4 py-3 shadow-sm
                   focus:border-emerald-500 focus:ring-emerald-500" placeholder="08xxxxxxxxxx" />
            <x-input-error :messages="$errors->get('phone')" class="mt-2" />
        </div>

        <div>
            <label for="city" class="block text-sm font-semibold text-gray-700 mb-1">
                Kota Domisili
            </label>
            <input id="city" type="text" name="city" value="{{ old('city') }}" required class="w-full rounded-xl border-gray-300 px-4 py-3 shadow-sm
                   focus:border-emerald-500 focus:ring-emerald-500" placeholder="Surabaya" />
            <x-input-error :messages="$errors->get('city')" class="mt-2" />
        </div>

        <div>
            <label for="address" class="block text-sm font-semibold text-gray-700 mb-1">
                Alamat Lengkap
            </label>
            <textarea id="address" name="address" rows="4" required class="w-full rounded-xl border-gray-300 px-4 py-3 shadow-sm
                   focus:border-emerald-500 focus:ring-emerald-500"
                placeholder="Jalan, nomor rumah, RT/RW...">{{ old('address') }}</textarea>
            <x-input-error :messages="$errors->get('address')" class="mt-2" />
        </div>

        <div>
            <label for="password" class="block text-sm font-semibold text-gray-700 mb-1">
                Password
            </label>
            <input id="password" type="password" name="password" required autocomplete="new-password" class="w-full rounded-xl border-gray-300 px-4 py-3 shadow-sm
                   focus:border-emerald-500 focus:ring-emerald-500" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div>
            <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-1">
                Konfirmasi Password
            </label>
            <input id="password_confirmation" type="password" name="password_confirmation" required
                autocomplete="new-password" class="w-full rounded-xl border-gray-300 px-4 py-3 shadow-sm
                   focus:border-emerald-500 focus:ring-emerald-500" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex flex-col sm:flex-row items-center justify-between gap-4 pt-4">
            <a href="{{ route('login') }}" class="text-sm text-gray-600 hover:text-emerald-600 underline">
                Sudah punya akun?
            </a>

            <button type="submit" class="w-full sm:w-auto bg-emerald-600 text-white px-8 py-3 rounded-xl
                   font-bold hover:bg-emerald-700 transition shadow-lg shadow-emerald-200">
                Daftar Sekarang
            </button>
        </div>
    </form>

</x-guest-layout>