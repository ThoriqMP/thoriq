@props(['value'])

<label {{ $attributes->merge(['class' => 'block text-xs font-semibold text-slate-350 uppercase tracking-wider']) }}>
    {{ $value ?? $slot }}
</label>
