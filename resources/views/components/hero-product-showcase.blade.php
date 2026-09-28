@props(['products'])

@php
    $preferredCodes = ['K03', 'K07'];
    $firstSlide = collect($preferredCodes)
        ->map(fn ($code) => $products->firstWhere('code', $code))
        ->filter()
        ->values();
    $remaining = $products->reject(fn ($product) => in_array($product['code'], $preferredCodes, true));
    $slides = collect();

    if ($firstSlide->isNotEmpty()) {
        $slides->push($firstSlide);
    }

    $remaining->groupBy('brand')->each(function ($brandProducts) use ($slides) {
        $brandProducts->values()->chunk(2)->each(fn ($pair) => $slides->push($pair->values()));
    });
@endphp

<div class="hero-visual hero-visual--rotator" aria-label="Alternatif facial wash tervalidasi" data-product-rotator>
    <div class="hero-arch" aria-hidden="true"></div>

    @foreach($slides as $slide)
        @php
            $primary = $slide->first();
            $secondary = $slide->skip(1)->first();
            $brands = $slide->pluck('brand')->unique()->join(' + ');
            $active = $loop->first;
        @endphp
        <div
            class="hero-showcase-slide {{ $active ? 'is-active' : '' }} {{ $secondary ? '' : 'is-single' }}"
            data-showcase-slide
            data-showcase-position="{{ $loop->index }}"
            aria-hidden="{{ $active ? 'false' : 'true' }}"
        >
            <div class="hero-product-stage">
                <div class="hero-pedestal" aria-hidden="true"></div>

                @if($secondary)
                    <figure class="hero-kahf-product hero-kahf-product--secondary" data-showcase-product="secondary" data-code="{{ $secondary['code'] }}">
                        <img
                            src="{{ $secondary['src'] }}"
                            alt="{{ $secondary['alt'] }}"
                            width="720"
                            height="720"
                            loading="{{ $active ? 'eager' : 'lazy' }}"
                            decoding="async"
                        >
                    </figure>
                @endif

                <figure class="hero-kahf-product hero-kahf-product--primary" data-showcase-product="primary" data-code="{{ $primary['code'] }}">
                    <img
                        src="{{ $primary['src'] }}"
                        alt="{{ $primary['alt'] }}"
                        width="720"
                        height="720"
                        loading="{{ $active ? 'eager' : 'lazy' }}"
                        decoding="async"
                    >
                </figure>
            </div>

            <div class="hero-kahf-caption">
                <small>{{ $brands }} / ALTERNATIF {{ str_pad((string) ($loop->index + 1), 2, '0', STR_PAD_LEFT) }}</small>
                <strong>{{ $secondary ? 'Dua pilihan dengan formula terdokumentasi.' : 'Pilihan dengan formula terdokumentasi.' }}</strong>
                <span>{{ $primary['name'] }} @if($secondary) <i></i> {{ $secondary['name'] }} @endif</span>
            </div>

            <span class="hero-note">KATALOG TERVERIFIKASI <i></i> {{ $brands }}</span>
        </div>
    @endforeach

    @if($slides->count() > 1)
        <button class="hero-showcase-toggle" type="button" data-showcase-toggle aria-pressed="false" aria-label="Jeda pergantian produk">
            <span data-showcase-toggle-label>JEDA</span>
        </button>
    @endif
</div>
