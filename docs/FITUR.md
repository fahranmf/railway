# FITUR DAN PEMBAGIAN TUGAS

Dokumen ini menjelaskan pembagian pengerjaan fitur pada proyek sistem apotek berdasarkan struktur kerja yang telah disepakati. Setiap orang memiliki bagian dan tanggung jawab kode untuk memastikan modul dapat terhubung dan berjalan secara terintegrasi.

---

## 1. 4523210020 - Arga Bona Simarmata (Ketua dan Project Manager)

Bagian ini mencakup persiapan awal sistem dan pengaturan fitur autentikasi serta manajemen pengguna.

### Tanggung Jawab:
- Konfigurasi awal Laravel dan database users (bawaan Laravel).
- Implementasi role user menggunakan Enum.
- Pembuatan resource Filament untuk CRUD pengguna.
- Penambahan role dalam tabel user melalui migration.

### File yang dikerjakan:
- `app/Enums/UserRole.php`
- `app/Models/User.php`
- `app/Filament/Resources/Users/...` (Form, Table, Pages, Resource)
- `database/migrations/..._add_role_to_users_table.php`
- `database/migrations/0001_01_01_000000_create_users_table.php`

---

## 2. 4523210049 — Firdaus Fatan Nugraha

Bagian ini menangani data dasar sistem seperti kategori, supplier, dan satuan barang yang menjadi fondasi modul lain.

### Tanggung Jawab:
- Pembuatan model dan resource CRUD kategori, supplier, dan unit.

### File yang dikerjakan:
- `app/Models/Category.php`
- `app/Models/Unit.php`
- `app/Models/Supplier.php`
- `app/Filament/Resources/Categories/...`
- `app/Filament/Resources/Units/...`
- `app/Filament/Resources/Suppliers/...`
- `database/migrations/..._create_categories_table.php`
- `database/migrations/..._create_units_table.php`
- `database/migrations/..._create_suppliers_table.php`

---

## 3. 4523210011 — Alyshia Cagivani Yasmin

Fokus pada modul inti produk serta tampilan dashboard.

### Tanggung Jawab:
- Menyusun struktur model dan resource CRUD produk.

### File yang dikerjakan:
- `app/Models/Product.php` (Versi awal)
- `app/Filament/Resources/Products/...` (tanpa RelationManagers)
- `database/migrations/..._create_products_table.php`

---

## 4. 4523210034 — Dendy Anugrahi Rabbi

Memperluas modul produk dengan batch dan logika otomatisasi.

### Tanggung Jawab:
- Menambahkan model dan relasi batch pada produk.
- Membuat Relation Manager untuk batch di Filament.

### File yang dikerjakan:
- `app/Models/ProductBatch.php`
- `app/Models/Product.php` (versi update dengan relasi batch)
- `app/Filament/Resources/Products/RelationManagers/BatchesRelationManager.php`
- `database/migrations/..._create_product_batches_table.php`

---

## 5. 4523210044 — Fahran Maulana Febryan

Bagian ini mencakup fitur transaksi penjualan serta sistem cetak struk.

### Tanggung Jawab:
- Membuat model dan resource transaksi serta item transaksi.

### File yang dikerjakan:
- `app/Models/Transaction.php`
- `app/Models/TransactionItem.php`
- `app/Filament/Resources/Transactions/...`
- `database/migrations/..._create_transactions_table.php`
- `database/migrations/..._create_transaction_items_table.php`

---

## 6. 4523210037 — Diva Cahya Hakim

Bagian ini berfokus pada peningkatan kejelasan dokumentasi serta implementasi keamanan akses produk.

### Tanggung Jawab:
- Menyusun dokumentasi agar sistem mudah dipahami dan dipelihara.
- Menentukan aturan akses produk melalui policy.

### File yang dikerjakan:
- `docs/` (semua isi)
- `README.md`

---

### Catatan

- Semua kontribusi harus sesuai standar struktur repositori dan mengikuti style guide yang telah disepakati.
- Setiap modul harus dapat terhubung mulus dengan modul lain tanpa perubahan yang merusak struktur database atau relasi.

---

### Status Kolaborasi

Dokumen ini akan diperbarui setiap kali perubahan struktur, fitur, atau pembagian tugas dilakukan agar seluruh anggota memiliki referensi yang konsisten.

