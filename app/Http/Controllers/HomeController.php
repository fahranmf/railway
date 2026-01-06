<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        // 1. Ambil Kategori untuk menu filter (Pill Menu)
        $categories = Category::all();

        // 2. Query Produk
        // Load relasi 'category' dan 'batches' agar accessor 'total_stock' efisien
        $query = Product::with(['category', 'batches'])->where('is_active', true);

        // Filter: Pencarian Nama
        if ($request->has('search') && $request->search != '') {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Filter: Kategori
        if ($request->has('category') && $request->category != '') {
            $query->where('category_id', $request->category);
        }

        $products = $query->latest()->get();

        // 3. Hitung Data untuk Floating Cart Button
        $globalCartCount = 0;
        $globalTotal = 0;

        if (Auth::check()) {
            // Ambil keranjang user yang sedang login
            $carts = Cart::with('product')->where('user_id', Auth::id())->get();

            // Hitung total item
            $globalCartCount = $carts->sum('quantity');

            // Hitung total harga
            $globalTotal = $carts->sum(function($cart) {
                return $cart->product->price * $cart->quantity;
            });
        }

        return view('frontend.home', compact('products', 'categories', 'globalCartCount', 'globalTotal'));
    }
}
