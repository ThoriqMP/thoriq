<x-app-layout>
    <x-slot name="header">
        <h2 class="font-extrabold text-2xl text-white leading-tight">
            {{ __('Profil Saya') }}
        </h2>
    </x-slot>

    <div class="space-y-8 max-w-3xl">
        <!-- Update Profile Information -->
        <div class="p-6 sm:p-8 bg-slate-900/30 border border-white/5 rounded-2xl shadow-xl">
            @include('profile.partials.update-profile-information-form')
        </div>

        <!-- Update Password -->
        <div class="p-6 sm:p-8 bg-slate-900/30 border border-white/5 rounded-2xl shadow-xl">
            @include('profile.partials.update-password-form')
        </div>

        <!-- Delete Account -->
        <div class="p-6 sm:p-8 bg-slate-900/30 border border-white/5 rounded-2xl shadow-xl">
            @include('profile.partials.delete-user-form')
        </div>
    </div>
</x-app-layout>
