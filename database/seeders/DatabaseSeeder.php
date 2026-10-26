<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ---- Akun demo (sama seperti README P4). HANYA untuk pengembangan lokal! ----
        $admin = User::factory()->admin()->create([
            'username' => 'admin', 'full_name' => 'Administrator', 'password' => 'admin123',
        ]);
        $staff = User::factory()->staff()->create([
            'username' => 'staff', 'full_name' => 'Staff Gudang', 'password' => 'staff123',
        ]);

        // ---- Kategori ----
        $categories = collect(['Elektronik', 'Alat Tulis', 'Perabot Kantor', 'Kebersihan', 'Perlengkapan IT'])
            ->map(fn (string $name) => Category::factory()->create([
                'name' => $name, 'description' => "Kategori {$name}",
            ]));

        // ---- Supplier ----
        $suppliers = Supplier::factory(6)->create();

        // ---- Produk (+ relasi supplier + stok yang konsisten dengan histori) ----
        Product::factory(30)
            ->recycle($categories)
            ->create()
            ->each(function (Product $product) use ($suppliers, $admin, $staff) {
                // 1–3 supplier per produk, masing-masing punya harga
                $sync = $suppliers->random(random_int(1, 3))
                    ->mapWithKeys(fn (Supplier $s) => [
                        $s->id => ['supplier_price' => round($product->price * fake()->randomFloat(2, 0.6, 0.9), 2)],
                    ])->all();
                $product->suppliers()->sync($sync);

                // Barang masuk awal
                $stock = random_int(0, 80);
                if ($stock > 0) {
                    StockMovement::factory()->in()->create([
                        'product_id' => $product->id,
                        'user_id'    => $admin->id,
                        'quantity'   => $stock,
                        'notes'      => 'Stok awal',
                    ]);
                }

                // Barang keluar (tidak pernah melebihi stok)
                if ($stock > 5 && fake()->boolean(70)) {
                    $out = random_int(1, (int) floor($stock / 2));
                    StockMovement::factory()->out()->create([
                        'product_id' => $product->id,
                        'user_id'    => $staff->id,
                        'quantity'   => $out,
                        'notes'      => 'Pengeluaran barang',
                    ]);
                    $stock -= $out;
                }

                $product->update(['stock' => $stock]);   // stok = total IN − total OUT
            });
    }
}
