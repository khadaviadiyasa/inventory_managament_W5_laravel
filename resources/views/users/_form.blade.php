@csrf
<div class="grid-2">
    <x-form.input name="username" label="Username" :value="$user->username ?? null" required maxlength="50" autocomplete="off" />
    <x-form.input name="full_name" label="Nama Lengkap" :value="$user->full_name ?? null" required maxlength="100" />
</div>
<div class="grid-2">
    <x-form.select name="role" label="Role"
        :options="['staff' => 'Staff', 'admin' => 'Admin']"
        :value="$user->role ?? 'staff'" />
    <div>
        <x-form.input name="password"
            :label="isset($user) ? 'Password Baru (kosongkan jika tidak diganti)' : 'Password'"
            type="password" :required="! isset($user)" minlength="8" autocomplete="new-password" />
    </div>
</div>
<div class="actions">
    <button type="submit" class="btn">Simpan</button>
    <a href="{{ route('users.index') }}" class="btn btn-light">Batal</a>
</div>
