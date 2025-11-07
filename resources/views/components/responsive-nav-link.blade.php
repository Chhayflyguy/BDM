@props(['active' => false, 'activeIs' => null])

@php
$isActive = ($active || ($activeIs && request()->routeIs($activeIs)));
$classes = $isActive
            ? 'block w-full ps-3 pe-4 py-2 border-l-4 border-white text-start text-base font-medium text-white focus:outline-none focus:text-white focus:border-white transition duration-150 ease-in-out'
            : 'block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-white hover:text-white hover:border-white focus:outline-none focus:text-white focus:border-white transition duration-150 ease-in-out';
$style = $isActive ? 'background-color: rgba(61, 109, 222, 0.3);' : '';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }} style="{{ $style }}" @if(!$isActive) onmouseover="this.style.backgroundColor='rgba(61, 109, 222, 0.2)';" onmouseout="this.style.backgroundColor='';" @endif>
    {{ $slot }}
</a>

