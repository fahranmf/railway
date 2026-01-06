<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\URL;

// Import Model yang dibutuhkan
use App\Models\Cart;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Observers\PurchaseObserver;
use App\Observers\PurchaseItemObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // self::registerIcons();
        if (app()->environment('production')) {
            URL::forceScheme('https');
        }
        // --- 1. LOGIKA OBSERVER (Agar stok update otomatis) ---
        Purchase::observe(PurchaseObserver::class);
        PurchaseItem::observe(PurchaseItemObserver::class);


        // --- 2. LOGIKA KERANJANG (Agar Floating Island & Counter muncul di semua halaman) ---
        View::composer('*', function ($view) {
            $count = 0;
            $total = 0;

            // Cek jika user sedang login
            if (Auth::check()) {
                // Ambil data keranjang user
                $carts = Cart::with('product')->where('user_id', Auth::id())->get();

                // Hitung total quantity barang
                $count = $carts->sum('quantity');

                // Hitung total harga belanjaan
                $total = $carts->sum(function ($item) {
                    return $item->product->price * $item->quantity;
                });
            }

            // Kirim variabel ke semua view (Global)
            $view->with('globalCartCount', $count);
            $view->with('globalTotal', $total);
        });
    }
}
