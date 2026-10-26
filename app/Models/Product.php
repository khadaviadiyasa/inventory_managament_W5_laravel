<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id', 'sku', 'name', 'description',
        'price', 'stock', 'minimum_stock',
    ];

    protected function casts(): array
    {
        return [
            'price'         => 'decimal:2',
            'stock'         => 'integer',
            'minimum_stock' => 'integer',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /** Many-to-many via tabel product_suppliers (kolom tambahan: supplier_price). */
    public function suppliers(): BelongsToMany
    {
        return $this->belongsToMany(Supplier::class, 'product_suppliers')
            ->withPivot('supplier_price');
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    /** Aturan P4: stok rendah jika stock <= minimum_stock. */
    public function isLowStock(): bool
    {
        return $this->stock <= $this->minimum_stock;
    }
}
