<section class="space-y-6">
    <header class="space-y-1">
        <h3 class="text-base font-bold text-rose-500">
            {{ __('Hapus Akun') }}
        </h3>
        <p class="text-xs text-slate-400 font-semibold">
            {{ __('Setelah akun Anda dihapus, semua data dan sumber daya di dalamnya akan dihapus secara permanen. Sebelum menghapus akun, harap unduh data atau informasi apa pun yang ingin Anda simpan.') }}
        </p>
    </header>

    <div class="pt-2">
        <x-danger-button
            x-data=""
            x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
        >
            {{ __('Hapus Akun Saya') }}
        </x-danger-button>
    </div>

    <!-- Confirmation Modal -->
    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6 space-y-6">
            @csrf
            @method('delete')

            <div class="space-y-2">
                <h3 class="text-lg font-bold text-rose-500">
                    {{ __('Apakah Anda yakin ingin menghapus akun?') }}
                </h3>
                <p class="text-xs text-slate-400 font-semibold leading-relaxed">
                    {{ __('Tindakan ini tidak dapat dibatalkan. Setelah akun dihapus, semua riwayat tugas, board Kanban, dan dokumen terenkripsi Anda akan terhapus selamanya. Silakan masukkan kata sandi Anda untuk mengonfirmasi.') }}
                </p>
            </div>

            <!-- Password Input -->
            <div class="space-y-2 max-w-md">
                <x-input-label for="password" value="{{ __('Password Anda') }}" class="sr-only" />
                <x-text-input
                    id="password"
                    name="password"
                    type="password"
                    placeholder="Masukkan password Anda untuk konfirmasi"
                    class="w-full"
                />
                <x-input-error :messages="$errors->userDeletion->get('password')" />
            </div>

            <!-- Footer Buttons -->
            <div class="flex justify-end gap-3 pt-2">
                <x-secondary-button x-on:click="$dispatch('close')">
                    {{ __('Batal') }}
                </x-secondary-button>

                <x-danger-button>
                    {{ __('Hapus Permanen') }}
                </x-danger-button>
            </div>
        </form>
    </x-modal>
</section>
