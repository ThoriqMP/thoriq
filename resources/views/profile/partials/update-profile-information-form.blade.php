<section class="space-y-6">
    <header class="space-y-1">
        <h3 class="text-base font-bold text-white">
            {{ __('Informasi Profil') }}
        </h3>
        <p class="text-xs text-slate-400 font-semibold">
            {{ __("Perbarui nama lengkap dan nama pengguna (username) akun Anda.") }}
        </p>
    </header>

    <form method="post" action="{{ route('profile.update') }}" class="space-y-5">
        @csrf
        @method('patch')

        <!-- Full Name -->
        <div class="space-y-2">
            <x-input-label for="name" :value="__('Nama Lengkap')" />
            <x-text-input id="name" name="name" type="text" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" />
        </div>

        <!-- Username -->
        <div class="space-y-2">
            <x-input-label for="username" :value="__('Username')" />
            <x-text-input id="username" name="username" type="text" :value="old('username', $user->username)" required autocomplete="username" />
            <x-input-error :messages="$errors->get('username')" />
        </div>

        <!-- Save button and message -->
        <div class="flex items-center gap-4 pt-2">
            <x-primary-button>{{ __('Simpan Perubahan') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-xs text-indigo-400 font-bold flex items-center gap-1"
                >
                    <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>{{ __('Perubahan profil berhasil disimpan.') }}</span>
                </p>
            @endif
        </div>
    </form>
</section>
