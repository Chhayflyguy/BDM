@props(['active' => false, 'activeIs' => null])

@php
$classes = ($active || ($activeIs && request()->routeIs($activeIs)))
            ? 'inline-flex items-center px-3 py-2 rounded-lg text-sm font-medium leading-5 text-white bg-white/20 backdrop-blur-sm border border-white/30 focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 transition duration-150 ease-in-out'
            : 'inline-flex items-center px-3 py-2 rounded-lg text-sm font-medium leading-5 text-white/90 hover:text-white hover:bg-white/10 hover:border-white/20 border border-transparent focus:outline-none focus:text-white focus:border-white/30 transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>

