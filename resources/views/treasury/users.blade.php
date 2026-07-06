<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h2 class="text-2xl font-extrabold text-white tracking-tight flex items-center gap-2">
                    <span class="p-2 bg-indigo-500/10 rounded-lg text-indigo-400">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 01-7.5 0M4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                        </svg>
                    </span>
                    Manajemen Pengguna & Peran
                </h2>
                <p class="text-xs text-slate-400 mt-1">Kelola Informasi Profil, Alamat Email, dan Hak Akses Peran Organisasi Tim</p>
            </div>
            @if(Auth::user()->role && Auth::user()->role->name === 'Treasury')
                <button onclick="document.getElementById('modal-user').classList.remove('hidden')" class="px-5 py-2.5 bg-indigo-650 hover:bg-indigo-600 text-white text-xs font-bold rounded-xl transition shadow-lg shadow-indigo-600/10 cursor-pointer flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Tambah Pengguna
                </button>
            @endif
        </div>
    </x-slot>

    <div class="space-y-6">
        <!-- List Users Grid -->
        <div class="bg-slate-900/40 border border-white/5 rounded-2xl overflow-hidden">
            <div class="p-6 border-b border-white/5">
                <h3 class="text-sm font-bold text-white uppercase tracking-wider">Anggota Tim Terdaftar</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-white/5 bg-slate-950/40 text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                            <th class="p-4">Nama Lengkap</th>
                            <th class="p-4">Username</th>
                            <th class="p-4">Email</th>
                            <th class="p-4">Peran (Role)</th>
                            @if(Auth::user()->role && Auth::user()->role->name === 'Treasury')
                                <th class="p-4 text-center">Aksi</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @foreach($users as $user)
                            <tr class="text-xs hover:bg-white/5 transition-colors">
                                <td class="p-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-indigo-500/15 text-indigo-400 flex items-center justify-center font-bold text-xs uppercase">
                                            {{ substr($user->name, 0, 2) }}
                                        </div>
                                        <div class="font-bold text-white">{{ $user->name }}</div>
                                    </div>
                                </td>
                                <td class="p-4 text-slate-350">@&ZeroWidthSpace;{{ $user->username }}</td>
                                <td class="p-4 text-slate-400">{{ $user->email }}</td>
                                <td class="p-4">
                                    <span class="px-2.5 py-0.5 text-[9px] font-bold uppercase rounded-lg border {{ $user->role && $user->role->name === 'Treasury' ? 'bg-indigo-500/10 text-indigo-400 border-indigo-500/10' : ($user->role && $user->role->name === 'Head' ? 'bg-violet-500/10 text-violet-400 border-violet-500/10' : ($user->role && $user->role->name === 'Sales' ? 'bg-emerald-500/10 text-emerald-400 border-emerald-500/10' : 'bg-slate-500/10 text-slate-400 border-slate-500/10')) }}">
                                        {{ $user->role ? $user->role->name : 'No Role' }}
                                    </span>
                                </td>
                                @if(Auth::user()->role && Auth::user()->role->name === 'Treasury')
                                    <td class="p-4 text-center flex items-center justify-center gap-2">
                                        <button 
                                            onclick="openEditModal({{ $user->id }}, '{{ $user->name }}', '{{ $user->role_id }}')" 
                                            class="p-1.5 hover:bg-indigo-500/15 text-indigo-450 hover:text-indigo-400 rounded-lg transition cursor-pointer" 
                                            title="Edit Peran">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                            </svg>
                                        </button>
                                        @if($user->id !== Auth::id())
                                            <form action="{{ route('treasury.users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengguna ini?')" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-1.5 hover:bg-rose-500/15 text-rose-400 rounded-lg transition cursor-pointer" title="Hapus User">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                                    </svg>
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-slate-600 font-semibold italic text-[10px] px-1.5">Aktif</span>
                                        @endif
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Form (Tambah User Baru) -->
    <div id="modal-user" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/85 backdrop-blur-sm hidden">
        <div class="bg-slate-900 border border-white/5 rounded-2xl w-full max-w-md p-6 space-y-6">
            <div class="flex justify-between items-center">
                <h3 class="text-base font-bold text-white">Tambah Pengguna Baru</h3>
                <button onclick="document.getElementById('modal-user').classList.add('hidden')" class="p-1 rounded-lg text-slate-400 hover:text-white hover:bg-white/5 cursor-pointer">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form action="{{ route('treasury.users.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-450 uppercase mb-2">Nama Lengkap</label>
                    <input type="text" name="name" required placeholder="Contoh: Thoriq Muhammad Pasya" class="w-full bg-slate-950 border border-white/5 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-indigo-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-450 uppercase mb-2">Username</label>
                    <input type="text" name="username" required placeholder="Contoh: thoriq_pasya" class="w-full bg-slate-950 border border-white/5 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-indigo-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-450 uppercase mb-2">Email</label>
                    <input type="email" name="email" required placeholder="Contoh: user@company.com" class="w-full bg-slate-950 border border-white/5 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-indigo-500">
                </div>

                <!-- Peran (Role) Organisasi dengan opsi custom role -->
                <div x-data="{ customRole: false }">
                    <label class="block text-xs font-bold text-slate-450 uppercase mb-2">Peran (Role) Organisasi</label>
                    <select name="role_id" @change="customRole = ($event.target.value === 'custom')" class="w-full bg-slate-950 border border-white/5 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-indigo-500">
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}">{{ $role->name }}</option>
                        @endforeach
                        <option value="custom">+ Tambah Peran Baru...</option>
                    </select>
                    
                    <div x-show="customRole" class="mt-3" style="display: none;">
                        <input type="text" name="custom_role" placeholder="Ketik nama peran baru..." class="w-full bg-slate-950 border border-white/5 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-indigo-500">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-450 uppercase mb-2">Kata Sandi (Password)</label>
                    <input type="password" name="password" required placeholder="Kata sandi default..." class="w-full bg-slate-950 border border-white/5 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-indigo-500">
                </div>

                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" onclick="document.getElementById('modal-user').classList.add('hidden')" class="px-4 py-2.5 bg-white/5 text-slate-350 hover:bg-white/10 hover:text-white rounded-xl text-xs font-semibold cursor-pointer">Batal</button>
                    <button type="submit" class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl text-xs font-bold cursor-pointer">Tambah User</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Form (Edit User Peran) -->
    <div id="modal-edit-user" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/85 backdrop-blur-sm hidden">
        <div class="bg-slate-900 border border-white/5 rounded-2xl w-full max-w-md p-6 space-y-6">
            <div class="flex justify-between items-center">
                <div>
                    <h3 class="text-base font-bold text-white">Edit Peran Anggota</h3>
                    <p id="edit-user-name" class="text-[11px] text-slate-400 mt-1"></p>
                </div>
                <button onclick="document.getElementById('modal-edit-user').classList.add('hidden')" class="p-1 rounded-lg text-slate-400 hover:text-white hover:bg-white/5 cursor-pointer">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form id="form-edit-user" method="POST" class="space-y-4" x-data="{ customEditRole: false }">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-xs font-bold text-slate-450 uppercase mb-2">Peran (Role) Organisasi</label>
                    <select id="edit-user-role" name="role_id" @change="customEditRole = ($event.target.value === 'custom')" class="w-full bg-slate-950 border border-white/5 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-indigo-500">
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}">{{ $role->name }}</option>
                        @endforeach
                        <option value="custom">+ Tambah Peran Baru...</option>
                    </select>

                    <div x-show="customEditRole" class="mt-3" style="display: none;">
                        <input type="text" name="custom_role" placeholder="Ketik nama peran baru..." class="w-full bg-slate-950 border border-white/5 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-indigo-500">
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" onclick="document.getElementById('modal-edit-user').classList.add('hidden')" class="px-4 py-2.5 bg-white/5 text-slate-350 hover:bg-white/10 hover:text-white rounded-xl text-xs font-semibold cursor-pointer">Batal</button>
                    <button type="submit" class="px-4 py-2.5 bg-indigo-650 hover:bg-indigo-600 text-white rounded-xl text-xs font-bold cursor-pointer">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit User Modal JS Helper -->
    <script>
        function openEditModal(userId, name, currentRoleId) {
            document.getElementById('edit-user-name').innerText = "Anggota: " + name;
            document.getElementById('edit-user-role').value = currentRoleId;
            
            // Set Form action dynamically
            const form = document.getElementById('form-edit-user');
            form.action = `/treasury/users/${userId}`;
            
            document.getElementById('modal-edit-user').classList.remove('hidden');
        }
    </script>
</x-app-layout>
