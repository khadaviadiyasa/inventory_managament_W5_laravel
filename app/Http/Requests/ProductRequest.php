<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;

class ProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Hak akses (admin) sudah dijaga oleh middleware role:admin pada route.
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'category_id'     => ['required', 'integer', 'exists:categories,id'],
            'sku'             => [
                'required', 'string', 'max:50',
                Rule::unique('products', 'sku')->ignore($this->route('product')),
            ],
            'name'            => ['required', 'string', 'max:150'],
            'description'     => ['nullable', 'string'],
            'price'           => ['required', 'numeric', 'min:0', 'max:9999999999.99'],
            'minimum_stock'   => ['required', 'integer', 'min:0'],
            // Stok TIDAK boleh diubah dari form produk: hanya melalui transaksi stok (aturan P4).
            'supplier_ids'    => ['required', 'array', 'min:1'],
            'supplier_ids.*'  => ['integer', 'distinct', 'exists:suppliers,id'],
            'supplier_price'  => ['required', 'array'],
        ];

        // Setiap supplier yang dipilih wajib punya harga >= 0.
        foreach ((array) $this->input('supplier_ids', []) as $supplierId) {
            if (is_numeric($supplierId)) {
                $rules["supplier_price.$supplierId"] = ['required', 'numeric', 'min:0'];
            }
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'category_id.required'  => 'Silakan pilih kategori yang tersedia.',
            'category_id.exists'    => 'Silakan pilih kategori yang tersedia.',
            'sku.required'          => 'SKU wajib diisi.',
            'sku.max'               => 'SKU maksimal 50 karakter.',
            'sku.unique'            => 'SKU sudah digunakan produk lain.',
            'name.required'         => 'Nama produk wajib diisi.',
            'name.max'              => 'Nama produk maksimal 150 karakter.',
            'price.required'        => 'Harga wajib diisi.',
            'price.numeric'         => 'Harga harus berupa angka.',
            'price.min'             => 'Harga tidak boleh negatif.',
            'minimum_stock.required' => 'Stok minimum wajib diisi.',
            'minimum_stock.integer' => 'Stok minimum harus berupa bilangan bulat nol atau lebih.',
            'minimum_stock.min'     => 'Stok minimum harus berupa bilangan bulat nol atau lebih.',
            'supplier_ids.required' => 'Pilih minimal satu supplier.',
            'supplier_ids.min'      => 'Pilih minimal satu supplier.',
            'supplier_ids.*.exists' => 'Supplier yang dipilih tidak ditemukan.',
            'supplier_price.required'   => 'Harga supplier wajib diisi.',
            'supplier_price.*.required' => 'Harga supplier wajib diisi.',
            'supplier_price.*.numeric'  => 'Harga supplier harus berupa angka nol atau lebih.',
            'supplier_price.*.min'      => 'Harga supplier harus berupa angka nol atau lebih.',
        ];
    }

    /** Data kolom tabel products (tanpa stok). */
    public function productData(): array
    {
        return Arr::only($this->validated(), [
            'category_id', 'sku', 'name', 'description', 'price', 'minimum_stock',
        ]);
    }

    /** Format untuk sync(): [supplier_id => ['supplier_price' => x]] */
    public function supplierSyncData(): array
    {
        $validated = $this->validated();
        $sync = [];

        foreach (array_unique($validated['supplier_ids']) as $id) {
            $sync[(int) $id] = ['supplier_price' => $validated['supplier_price'][$id]];
        }

        return $sync;
    }
}
