@props(['localeUrls'])

@php
    $currentLocale = app()->getLocale();
    $flags = [
        'pt' => 'br',
        'en' => 'us',
        'es' => 'es',
        'it' => 'it',
    ];
    $instanceId = 'locale-flags-'.str_replace('.', '-', uniqid('', true));
@endphp

<nav {{ $attributes->class(['flex items-center gap-1 sm:gap-1.5']) }} aria-label="Idiomas">
    @foreach ($flags as $locale => $country)
        @php
            $active = $locale === $currentLocale;
            $label = __('site.countries.'.$country);
            $tooltipId = $instanceId.'-'.$country.'-tooltip';
        @endphp
        <a href="{{ $localeUrls[$locale] }}"
           aria-label="{{ $label }}"
           aria-describedby="{{ $tooltipId }}"
           @if ($active) aria-current="page" @endif
           class="group relative grid rounded-md p-1 transition duration-200 ease-pib hover:bg-ouro-200/60 focus-visible:bg-ouro-200/60 {{ $active ? 'bg-ouro-200 ring-2 ring-ouro-500' : 'bg-white ring-1 ring-linen-300' }}">
            @switch($country)
                @case('br')
                    <svg class="h-[15px] w-[23px] sm:h-[18px] sm:w-[27px]" viewBox="0 0 30 20" aria-hidden="true">
                        <rect width="30" height="20" rx="1" fill="#229E45"/>
                        <path d="M15 2.8 27 10 15 17.2 3 10Z" fill="#F8D64E"/>
                        <circle cx="15" cy="10" r="4.5" fill="#2B4A9C"/>
                        <path d="M10.9 8.9c2.7-.5 5.6.1 7.8 1.7" fill="none" stroke="#fff" stroke-width=".75"/>
                    </svg>
                    @break
                @case('us')
                    <svg class="h-[15px] w-[23px] sm:h-[18px] sm:w-[27px]" viewBox="0 0 30 20" aria-hidden="true">
                        <rect width="30" height="20" rx="1" fill="#fff"/>
                        <path d="M0 0h30v1.55H0zm0 3.1h30v1.55H0zm0 3.1h30v1.55H0zm0 3.1h30v1.55H0zm0 3.1h30v1.55H0zm0 3.1h30v1.55H0zm0 3.1h30V20H0z" fill="#B22234"/>
                        <path d="M0 0h13v10.85H0z" fill="#3C3B6E"/>
                        <g fill="#fff"><circle cx="2" cy="2" r=".5"/><circle cx="5" cy="2" r=".5"/><circle cx="8" cy="2" r=".5"/><circle cx="11" cy="2" r=".5"/><circle cx="3.5" cy="4.3" r=".5"/><circle cx="6.5" cy="4.3" r=".5"/><circle cx="9.5" cy="4.3" r=".5"/><circle cx="2" cy="6.6" r=".5"/><circle cx="5" cy="6.6" r=".5"/><circle cx="8" cy="6.6" r=".5"/><circle cx="11" cy="6.6" r=".5"/><circle cx="3.5" cy="8.9" r=".5"/><circle cx="6.5" cy="8.9" r=".5"/><circle cx="9.5" cy="8.9" r=".5"/></g>
                    </svg>
                    @break
                @case('es')
                    <svg class="h-[15px] w-[23px] sm:h-[18px] sm:w-[27px]" viewBox="0 0 30 20" aria-hidden="true">
                        <rect width="30" height="20" rx="1" fill="#AA151B"/>
                        <path d="M0 5h30v10H0z" fill="#F1BF00"/>
                        <path d="M8 8.1h1.8v4H8z" fill="#AA151B" opacity=".8"/>
                    </svg>
                    @break
                @case('it')
                    <svg class="h-[15px] w-[23px] sm:h-[18px] sm:w-[27px]" viewBox="0 0 30 20" aria-hidden="true">
                        <rect width="30" height="20" rx="1" fill="#fff"/>
                        <path d="M0 0h10v20H0z" fill="#009246"/>
                        <path d="M20 0h10v20H20z" fill="#CE2B37"/>
                    </svg>
                    @break
            @endswitch

            <span id="{{ $tooltipId }}" role="tooltip"
                  class="pointer-events-none absolute left-1/2 top-full z-50 mt-2 -translate-x-1/2 whitespace-nowrap rounded-md bg-onyx-950 px-2.5 py-1.5 text-[11px] font-semibold normal-case tracking-normal text-white opacity-0 shadow-card transition-opacity duration-150 group-hover:opacity-100 group-focus-visible:opacity-100">
                {{ $label }}
            </span>
        </a>
    @endforeach
</nav>
