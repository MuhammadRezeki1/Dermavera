@props([
    'sidebar' => false,
])

<a {{ $attributes->class(['inline-flex items-center', 'px-2' => $sidebar]) }} aria-label="Dermavera">
    <img
        class="h-auto w-44 max-w-full object-contain"
        src="{{ asset('images/branding/ChatGPT Image Sep 24, 2026, 05_31_24 PM.png') }}"
        alt="Dermavera"
        width="2172"
        height="724"
    >
</a>
