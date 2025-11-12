@php
    $classes = $attributes->get('class', '');
    // Extract height class (h-*) to determine size, use same for width to make it circular
    $sizeClass = 'h-10 w-10'; // default size
    if (preg_match('/\bh-(\d+)\b/', $classes, $matches)) {
        $height = $matches[1];
        $sizeClass = "h-{$height} w-{$height}";
    } elseif (preg_match('/\bw-(\d+)\b/', $classes, $matches)) {
        $width = $matches[1];
        $sizeClass = "h-{$width} w-{$width}";
    }
    // Remove size classes and other conflicting classes from the original
    $cleanClasses = preg_replace('/\b(w-\d+|h-\d+|w-auto|block|fill-current|text-\S+)\b/', '', $classes);
    $cleanClasses = trim($cleanClasses);
@endphp
<div class="rounded-full overflow-hidden border-2 border-white/30 flex items-center justify-center shrink-0 {{ $sizeClass }}">
    <img src="{{ asset('images/logo.jpg') }}" alt="Company Logo" class="w-full h-full object-cover">
</div>