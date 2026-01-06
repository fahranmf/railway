<x-guest-layout>
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <h2 class="text-xl font-bold text-center text-gray-800 mb-6">Masuk ke Akun</h2>

    <form method="POST" action="{{ route('login') }}" class="space-y-6">
        @csrf

        <div>
            <label for="email" class="block text-sm font-semibold text-gray-700 mb-1">
                Email
            </label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                autocomplete="username" class="w-full rounded-xl border-gray-300 px-4 py-3 shadow-sm
                    focus:border-emerald-500 focus:ring-emerald-500" placeholder="email@example.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <label for="password" class="block text-sm font-semibold text-gray-700 mb-1">
                Password
            </label>
            <input id="password" type="password" name="password" required autocomplete="current-password" class="w-full rounded-xl border-gray-300 px-4 py-3 shadow-sm
                    focus:border-emerald-500 focus:ring-emerald-500" placeholder="••••••••" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between">
            <label for="remember_me" class="inline-flex items-center gap-2 text-sm text-gray-600">
                <input id="remember_me" type="checkbox" name="remember" class="rounded border-gray-300 text-emerald-600 shadow-sm
                        focus:ring-emerald-500" />
                Ingat saya
            </label>

            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="text-sm text-gray-600 hover:text-emerald-600 underline">
                    Lupa password?
                </a>
            @endif
        </div>

        <button type="submit" class="w-full bg-emerald-600 text-white py-3 rounded-xl
                font-bold hover:bg-emerald-700 transition
                shadow-lg shadow-emerald-200">
            Masuk
        </button>
    </form>

</x-guest-layout>