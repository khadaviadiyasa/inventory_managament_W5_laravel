<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));

        $query = User::query()->when($search !== '', function ($q) use ($search) {
            $q->where(function ($w) use ($search) {
                $w->where('username', 'like', "%{$search}%")
                  ->orWhere('full_name', 'like', "%{$search}%")
                  ->orWhere('role', 'like', "%{$search}%");
            });
        });

        $totals = [
            'users'  => (clone $query)->count(),
            'admins' => (clone $query)->where('role', User::ROLE_ADMIN)->count(),
            'staff'  => (clone $query)->where('role', User::ROLE_STAFF)->count(),
        ];

        $users = $query->orderByDesc('id')->paginate(10)->withQueryString();

        return view('users.index', compact('users', 'search', 'totals'));
    }

    public function create(): View
    {
        return view('users.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate($this->rules(), $this->messages());

        User::create($data);   // password otomatis di-hash oleh cast 'hashed'

        return redirect()->route('users.index')
            ->with('success', 'Pengguna baru berhasil ditambahkan.');
    }

    public function edit(User $user): View
    {
        return view('users.edit', compact('user'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate($this->rules($user), $this->messages());

        DB::transaction(function () use ($data, $user) {
            $locked = User::query()->lockForUpdate()->findOrFail($user->id);

            // Admin tidak boleh menurunkan role akunnya sendiri.
            if ($locked->id === Auth::id() && $data['role'] !== User::ROLE_ADMIN) {
                throw ValidationException::withMessages([
                    'role' => 'Role akun Admin yang sedang digunakan tidak dapat diubah menjadi Staff.',
                ]);
            }

            // Harus selalu ada minimal satu Admin.
            if (
                $locked->role === User::ROLE_ADMIN
                && $data['role'] !== User::ROLE_ADMIN
                && User::where('role', User::ROLE_ADMIN)->count() <= 1
            ) {
                throw ValidationException::withMessages([
                    'role' => 'Tidak dapat mengubah role karena harus ada minimal satu Admin.',
                ]);
            }

            // Password kosong = tidak diganti.
            if (empty($data['password'])) {
                unset($data['password']);
            }

            $locked->update($data);
        });

        return redirect()->route('users.index')
            ->with('success', 'Data pengguna berhasil diperbarui.');
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === Auth::id()) {
            return redirect()->route('users.index')
                ->with('error', 'Kamu tidak dapat menghapus akun yang sedang digunakan.');
        }

        try {
            DB::transaction(function () use ($user) {
                $locked = User::query()->lockForUpdate()->findOrFail($user->id);

                if (
                    $locked->role === User::ROLE_ADMIN
                    && User::where('role', User::ROLE_ADMIN)->count() <= 1
                ) {
                    throw ValidationException::withMessages([
                        'user' => 'Admin terakhir tidak dapat dihapus.',
                    ]);
                }

                $locked->delete();
            });
        } catch (ValidationException $e) {
            return redirect()->route('users.index')->with('error', $e->validator->errors()->first());
        } catch (QueryException $e) {
            // FK RESTRICT: pengguna sudah punya histori transaksi stok.
            return redirect()->route('users.index')
                ->with('error', 'Pengguna masih terhubung dengan histori transaksi sehingga tidak dapat dihapus.');
        }

        return redirect()->route('users.index')
            ->with('success', 'Pengguna berhasil dihapus.');
    }

    private function rules(?User $user = null): array
    {
        return [
            'username'  => [
                'required', 'regex:/^[A-Za-z0-9._-]{3,50}$/',
                Rule::unique('users', 'username')->ignore($user),
            ],
            'full_name' => ['required', 'string', 'max:100'],
            'role'      => ['required', Rule::in([User::ROLE_ADMIN, User::ROLE_STAFF])],
            // Akun baru: wajib. Edit: opsional (kosong = tidak diganti).
            'password'  => [$user ? 'nullable' : 'required', 'string', 'min:8'],
        ];
    }

    private function messages(): array
    {
        return [
            'username.required'  => 'Username wajib diisi.',
            'username.regex'     => 'Username harus 3–50 karakter dan hanya boleh berisi huruf, angka, titik, garis bawah, atau tanda hubung.',
            'username.unique'    => 'Username sudah digunakan.',
            'full_name.required' => 'Nama lengkap wajib diisi.',
            'full_name.max'      => 'Nama lengkap maksimal 100 karakter.',
            'role.required'      => 'Role pengguna tidak valid.',
            'role.in'            => 'Role pengguna tidak valid.',
            'password.required'  => 'Password wajib minimal 8 karakter.',
            'password.min'       => 'Password wajib minimal 8 karakter.',
        ];
    }
}
