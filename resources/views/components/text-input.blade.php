@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'block w-full px-4 py-3 bg-slate-950/80 border border-slate-800/80 rounded-xl text-slate-200 placeholder-slate-650 focus:outline-none focus:border-indigo-500/80 focus:ring-2 focus:ring-indigo-500/20 transition-all duration-200 shadow-sm']) }}>
