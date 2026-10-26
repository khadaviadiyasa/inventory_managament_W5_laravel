@csrf
<div class="grid-2">
    <x-form.input name="name" label="Nama Supplier" :value="$supplier->name ?? null" required maxlength="150" />
    <x-form.input name="contact_person" label="Nama Kontak" :value="$supplier->contact_person ?? null" maxlength="100" />
</div>
<div class="grid-2">
    <x-form.input name="phone" label="Telepon" :value="$supplier->phone ?? null" maxlength="30" />
    <x-form.input name="email" label="Email" type="email" :value="$supplier->email ?? null" maxlength="100" />
</div>
<x-form.textarea name="address" label="Alamat" :value="$supplier->address ?? null" />
<div class="actions">
    <button type="submit" class="btn">Simpan</button>
    <a href="{{ route('suppliers.index') }}" class="btn btn-light">Batal</a>
</div>
