@csrf
<x-form.input name="name" label="Nama Kategori" :value="$category->name ?? null" required maxlength="100" />
<x-form.textarea name="description" label="Deskripsi" :value="$category->description ?? null" />
<div class="actions">
    <button type="submit" class="btn">Simpan</button>
    <a href="{{ route('categories.index') }}" class="btn btn-light">Batal</a>
</div>
