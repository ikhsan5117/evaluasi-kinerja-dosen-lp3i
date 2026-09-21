<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return $this->redirectByRole(Auth::user()->role);
        }

        $kelasList = \App\Models\Mahasiswa::select('kelas')
            ->whereNotNull('kelas')
            ->where('kelas', '!=', '')
            ->distinct()
            ->orderBy('kelas')
            ->pluck('kelas');

        return view('auth.login', compact('kelasList'));
    }

    public function login(Request $request)
    {
        $identifier = trim((string) ($request->input('name') ?? $request->input('email') ?? $request->input('username') ?? ''));
        $password = $request->input('password');
        $kelas = trim((string) ($request->input('kelas') ?? ''));

        if (empty($identifier)) {
            return back()->withErrors(['error' => 'Nama, NIM, atau NIDN wajib diisi.'])->withInput($request->except('password'));
        }

        if (empty($password)) {
            return back()->withErrors(['error' => 'Password wajib diisi.'])->withInput($request->except('password'));
        }

        // Candidates collection to test password
        $candidates = collect();

        // 1. Check Admin matches (by name, email, or "admin" username)
        $adminUsers = \App\Models\User::where('role', 'admin')
            ->where(function ($q) use ($identifier) {
                $q->where('email', $identifier)
                    ->orWhere('name', $identifier)
                    ->orWhereRaw('LOWER(name) = ?', [strtolower($identifier)])
                    ->orWhereRaw('LOWER(email) = ?', [strtolower($identifier)]);
                if (in_array(strtolower($identifier), ['admin', 'administrator'])) {
                    $q->orWhere('role', 'admin');
                }
            })
            ->get();
        $candidates = $candidates->merge($adminUsers);

        // 2. Check Dosen matches (by name, email, or NIDN)
        $dosenUsers = \App\Models\User::where('role', 'dosen')
            ->where(function ($q) use ($identifier) {
                $q->where('email', $identifier)
                    ->orWhere('name', $identifier)
                    ->orWhereRaw('LOWER(name) LIKE ?', ['%' . strtolower($identifier) . '%'])
                    ->orWhereHas('dosen', function ($dq) use ($identifier) {
                        $dq->where('nidn', $identifier);
                    });
            })
            ->get();
        $candidates = $candidates->merge($dosenUsers);

        // 3. Check Mahasiswa matches (with optional kelas filter, by name, email, or NIM)
        $mahasiswaQuery = \App\Models\User::where('role', 'mahasiswa');
        if (!empty($kelas)) {
            $mahasiswaQuery->whereHas('mahasiswa', function ($mq) use ($kelas) {
                $mq->where('kelas', $kelas);
            });
        }
        $mahasiswaQuery->where(function ($q) use ($identifier) {
            $q->where('name', $identifier)
                ->orWhere('email', $identifier)
                ->orWhereRaw('LOWER(name) LIKE ?', ['%' . strtolower($identifier) . '%'])
                ->orWhereHas('mahasiswa', function ($mq) use ($identifier) {
                    $mq->where('nim', $identifier);
                });
        });
        $mahasiswaUsers = $mahasiswaQuery->get();

        // Fallback without kelas filter if empty
        if ($mahasiswaUsers->isEmpty()) {
            $mahasiswaUsers = \App\Models\User::where('role', 'mahasiswa')
                ->where(function ($q) use ($identifier) {
                    $q->where('name', $identifier)
                        ->orWhere('email', $identifier)
                        ->orWhereRaw('LOWER(name) LIKE ?', ['%' . strtolower($identifier) . '%'])
                        ->orWhereHas('mahasiswa', function ($mq) use ($identifier) {
                            $mq->where('nim', $identifier);
                        });
                })
                ->get();
        }
        $candidates = $candidates->merge($mahasiswaUsers);

        // 4. Also check direct email / name match across all users
        $directUsers = \App\Models\User::where('email', $identifier)->orWhere('name', $identifier)->get();
        $candidates = $candidates->merge($directUsers)->unique('id');

        // Verify password on matching candidates
        foreach ($candidates as $cand) {
            if (Hash::check($password, $cand->password)) {
                Auth::login($cand, $request->boolean('remember'));
                $request->session()->regenerate();
                return $this->redirectByRole($cand->role)->with('success', 'Selamat datang kembali, ' . $cand->name . '!');
            }
        }

        // Standard Auth::attempt fallback
        if (Auth::attempt(['email' => $identifier, 'password' => $password], $request->boolean('remember'))) {
            $request->session()->regenerate();
            $authUser = Auth::user();
            return $this->redirectByRole($authUser->role)->with('success', 'Selamat datang kembali, ' . $authUser->name . '!');
        }

        return back()->withErrors([
            'error' => 'Data login atau password yang Anda masukkan tidak sesuai.',
        ])->withInput($request->except('password'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda telah berhasil logout.');
    }

    public function profile()
    {
        $user = Auth::user();
        return view('profile.index', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'current_password' => 'nullable|required_with:new_password',
            'new_password' => 'nullable|min:6|confirmed',
        ]);

        $user->name = $validated['name'];
        $user->phone = $validated['phone'] ?? null;

        if (!empty($validated['new_password'])) {
            if (!Hash::check($validated['current_password'], $user->password)) {
                return back()->withErrors(['current_password' => 'Password saat ini tidak cocok.']);
            }
            $user->password = Hash::make($validated['new_password']);
        }

        $user->save();

        return back()->with('success', 'Profil berhasil diperbarui.');
    }

    public function autocompleteKelas(Request $request)
    {
        $q = trim((string) $request->input('q', ''));
        $classes = \App\Models\Mahasiswa::select('kelas')
            ->whereNotNull('kelas')
            ->where('kelas', '!=', '')
            ->when($q, function ($query, $q) {
                $query->where('kelas', 'like', "%{$q}%");
            })
            ->distinct()
            ->orderBy('kelas')
            ->pluck('kelas');

        return response()->json($classes);
    }

    public function autocompleteMahasiswa(Request $request)
    {
        $kelas = trim((string) $request->input('kelas', ''));
        $q = trim((string) $request->input('q', ''));

        $query = \App\Models\Mahasiswa::with('user:id,name,email')
            ->when($kelas, function ($query, $kelas) {
                $query->where('kelas', $kelas);
            })
            ->when($q, function ($query, $q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('nim', 'like', "%{$q}%")
                        ->orWhereHas('user', function ($uq) use ($q) {
                            $uq->where('name', 'like', "%{$q}%");
                        });
                });
            })
            ->limit(30)
            ->get()
            ->map(function ($m) {
                return [
                    'nim'   => $m->nim,
                    'name'  => $m->user?->name ?? $m->nim,
                    'kelas' => $m->kelas,
                ];
            });

        return response()->json($query);
    }

    public function autocompleteDosen(Request $request)
    {
        $q = trim((string) $request->input('q', ''));

        $query = \App\Models\Dosen::with('user:id,name,email')
            ->when($q, function ($query, $q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('nidn', 'like', "%{$q}%")
                        ->orWhereHas('user', function ($uq) use ($q) {
                            $uq->where('name', 'like', "%{$q}%");
                        });
                });
            })
            ->limit(30)
            ->get()
            ->map(function ($d) {
                $gelar = $d->gelar ? ', ' . $d->gelar : '';
                return [
                    'nidn'     => $d->nidn,
                    'name'     => ($d->user?->name ?? '') . $gelar,
                    'raw_name' => $d->user?->name ?? '',
                    'email'    => $d->user?->email ?? '',
                ];
            });

        return response()->json($query);
    }

    public function autocompleteUser(Request $request)
    {
        $kelas = trim((string) $request->input('kelas', ''));
        $q = trim((string) $request->input('q', ''));

        // If specific class is selected, show students for that class
        if (!empty($kelas)) {
            $mhs = \App\Models\Mahasiswa::with('user:id,name,email')
                ->where('kelas', $kelas)
                ->when($q, function ($query, $q) {
                    $query->where(function ($sub) use ($q) {
                        $sub->where('nim', 'like', "%{$q}%")
                            ->orWhereHas('user', function ($uq) use ($q) {
                                $uq->where('name', 'like', "%{$q}%");
                            });
                    });
                })
                ->limit(30)
                ->get()
                ->map(function ($m) {
                    return [
                        'type'     => 'mahasiswa',
                        'icon'     => '🎓',
                        'name'     => $m->user?->name ?? $m->nim,
                        'raw_name' => $m->user?->name ?? $m->nim,
                        'nim'      => $m->nim,
                        'kelas'    => $m->kelas,
                    ];
                });
            return response()->json($mhs);
        }

        // If no class filter and query is empty, return empty
        if (empty($q)) {
            return response()->json([]);
        }

        // Don't show hints if user types "admin" to avoid exposing admin credentials
        if (strtolower($q) === 'admin' || strtolower($q) === 'administrator') {
            return response()->json([]);
        }

        // Search Mahasiswa
        $mhs = \App\Models\Mahasiswa::with('user:id,name,email')
            ->where(function ($sub) use ($q) {
                $sub->where('nim', 'like', "%{$q}%")
                    ->orWhereHas('user', function ($uq) use ($q) {
                        $uq->where('name', 'like', "%{$q}%");
                    });
            })
            ->limit(15)
            ->get()
            ->map(function ($m) {
                return [
                    'type'     => 'mahasiswa',
                    'icon'     => '🎓',
                    'name'     => $m->user?->name ?? $m->nim,
                    'raw_name' => $m->user?->name ?? $m->nim,
                    'nim'      => $m->nim,
                    'kelas'    => $m->kelas,
                ];
            });

        // Search Dosen
        $dsn = \App\Models\Dosen::with('user:id,name,email')
            ->where(function ($sub) use ($q) {
                $sub->where('nidn', 'like', "%{$q}%")
                    ->orWhereHas('user', function ($uq) use ($q) {
                        $uq->where('name', 'like', "%{$q}%");
                    });
            })
            ->limit(15)
            ->get()
            ->map(function ($d) {
                $gelar = $d->gelar ? ', ' . $d->gelar : '';
                return [
                    'type'     => 'dosen',
                    'icon'     => '🧑‍🏫',
                    'name'     => ($d->user?->name ?? '') . $gelar,
                    'raw_name' => $d->user?->name ?? '',
                    'nidn'     => $d->nidn,
                    'email'    => $d->user?->email ?? '',
                    'kelas'    => null,
                ];
            });

        $results = $mhs->concat($dsn)->values();
        return response()->json($results);
    }

    protected function redirectByRole(string $role)
    {
        return match ($role) {
            'admin' => redirect()->route('admin.dashboard'),
            'dosen' => redirect()->route('dosen.dashboard'),
            'mahasiswa' => redirect()->route('mahasiswa.dashboard'),
            default => redirect('/'),
        };
    }
}
