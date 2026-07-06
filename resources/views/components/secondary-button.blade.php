<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center justify-center px-5 py-3 bg-slate-900 border border-slate-800 rounded-xl font-bold text-xs text-slate-350 hover:text-white hover:bg-white/5 uppercase tracking-widest focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:ring-offset-slate-950 transition-all duration-150 cursor-pointer']) }}>
    {{ $slot }}
</button>
