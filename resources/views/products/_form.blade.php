{{--
  Partial form produk (dipakai create & edit).
  Variabel: $product (opsional), $categories, $suppliers, $selectedSuppliers [supplier_id => harga]
--}}
@csrf

<div class="grid-2">
    <x-form.input name="sku" label="SKU" :value="$product->sku ?? null" required maxlength="50" />
    <x-form.input name="name" label="Nama Produk" :value="$product->name ?? null" required maxlength="150" />
</div>

<div class="grid-2">
    <x-form.select name="category_id" label="Kategori"
        :options="$categories->pluck('name', 'id')->all()"
        :value="$product->category_id ?? null"
        placeholder="-- Pilih kategori --" />
    <x-form.input name="price" label="Harga (Rp)" type="number" step="0.01" min="0" :value="$product->price ?? 0" required />
</div>

<div class="grid-2">
    <x-form.input name="minimum_stock" label="Stok Minimum" type="number" min="0" step="1" :value="$product->minimum_stock ?? 5" required />
    @isset($product)
        <div class="form-group">
            <label>Stok Saat Ini</label>
            <input type="text" value="{{ $product->stock }}" disabled>
            <small class="muted">Stok hanya berubah lewat menu Stok Masuk/Keluar.</small>
        </div>
    @endisset
</div>

<x-form.textarea name="description" label="Deskripsi" :value="$product->description ?? null" />

{{-- Many-to-many supplier + harga supplier --}}
@php
    $checkedIds = collect(old('supplier_ids', array_keys($selectedSuppliers)))->map(fn ($id) => (int) $id)->all();
@endphp
<div class="form-group">
    <label>Supplier <span class="req">*</span></label>
    @error('supplier_ids')<small class="error">{{ $message }}</small>@enderror
    @error('supplier_ids.*')<small class="error">{{ $message }}</small>@enderror
    @error('supplier_price')<small class="error">{{ $message }}</small>@enderror

    @forelse($suppliers as $supplier)
        <div class="supplier-row">
            <input type="checkbox" name="supplier_ids[]" id="supplier_{{ $supplier->id }}"
                   value="{{ $supplier->id }}" @checked(in_array($supplier->id, $checkedIds, true))>
            <label for="supplier_{{ $supplier->id }}" style="margin:0;font-weight:400">{{ $supplier->name }}</label>
            <div>
                <input type="number" step="0.01" min="0" name="supplier_price[{{ $supplier->id }}]"
                       value="{{ old('supplier_price.' . $supplier->id, $selectedSuppliers[$supplier->id] ?? 0) }}"
                       placeholder="Harga supplier"
                       @class(['is-invalid' => $errors->has('supplier_price.' . $supplier->id)])>
                @error('supplier_price.' . $supplier->id)<small class="error">{{ $message }}</small>@enderror
            </div>
        </div>
    @empty
        <p class="muted">Belum ada supplier. <a href="{{ route('suppliers.create') }}">Tambah supplier</a> terlebih dahulu.</p>
    @endforelse
</div>

<div class="actions">
    <button type="submit" class="btn">Simpan</button>
    <a href="{{ route('products.index') }}" class="btn btn-light">Batal</a>
</div>
