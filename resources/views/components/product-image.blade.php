@props(['variant', 'eager' => false])
@php($image = config('product-images.'.$variant->catalog_code))
@php($cutout = pathinfo($image['file'], PATHINFO_FILENAME).'.png')
<img
    {{ $attributes->class(['product-image']) }}
    src="{{ asset('images/product-cutout/'.$cutout) }}"
    alt="{{ $variant->name }} — kemasan depan"
    width="720"
    height="720"
    loading="{{ $eager ? 'eager' : 'lazy' }}"
    decoding="async"
>
