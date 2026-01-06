<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MidtransController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// --- 1. HALAMAN PUBLIK ---
Route::get('/', [HomeController::class, 'index'])->name('home');

// --- 2. HALAMAN USER (BUTUH LOGIN) ---
Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard User
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // --- FITUR KERANJANG (CART) ---
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add/{id}', [CartController::class, 'addToCart'])->name('cart.add');
    Route::delete('/cart/{id}', [CartController::class, 'destroy'])->name('cart.destroy');
    Route::patch('/cart/update/{id}', [CartController::class, 'update'])->name('cart.update');

    // --- FITUR CHECKOUT (PEMBAYARAN) ---
    // Use the FEFO-capable CheckoutController so ProductBatch is recorded on items
    Route::post('/checkout', [CheckoutController::class, 'process'])->name('cart.checkout');

    // --- FITUR HISTORY & DETAIL TRANSAKSI ---
    // Melihat Daftar Riwayat Belanja
    Route::get('/my-transactions', [TransactionController::class, 'index'])->name('transactions.index');
    // Melihat Detail Struk (Digital Invoice)
    Route::get('/my-transactions/{transaction}', [TransactionController::class, 'show'])->name('transactions.show');
    // Cetak Struk (PDF/Print View)
    Route::get('/transactions/print/{transaction}', [TransactionController::class, 'print'])->name('transactions.print');

    Route::get('/midtrans/pay/{transaction}', [MidtransController::class, 'pay'])
        ->name('midtrans.pay');

    // --- PROFILE BREEZE ---
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


Route::post('/midtrans/webhook', [MidtransController::class, 'notification']);


require __DIR__ . '/auth.php';
