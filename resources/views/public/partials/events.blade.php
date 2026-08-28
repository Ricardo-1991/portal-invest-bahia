@if ($events->isNotEmpty())
    <section x-data="{ modal: null }" class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
        <div class="mb-10 text-center">
            <span class="text-xs font-semibold uppercase tracking-[0.24em] text-ouro-700">@lang('site.events.eyebrow')</span>
            <h2 class="mt-3 font-display text-3xl text-onyx-950 md:text-4xl" data-reveal>@lang('site.events.title')</h2>
        </div>

        {{-- Carrossel coverflow (referência tasteskill): autoplay em loop,
             arraste para navegar, clique abre o modal de detalhes. --}}
        <div class="relative mx-auto max-w-3xl overflow-hidden px-4" style="touch-action: pan-y;"
             x-data="coverflow({{ $events->count() }})"
             @pointerdown="dragStart($event)"
             @pointermove="dragMove($event)"
             @pointerup="dragEnd($event)"
             @pointerleave="dragging && dragEnd($event)">

            <div class="relative h-80 select-none" style="perspective: 1200px;">
                @foreach ($events as $index => $event)
                    @php
                        $image = $event->imageUrl('thumb') ?: asset('images/property-placeholder.webp');
                        $data = [
                            'title' => $event->title,
                            'subtitle' => $event->subtitle,
                            'description' => $event->description,
                            'image' => $event->imageUrl() ?: $image,
                        ];
                    @endphp
                    <button type="button"
                            :style="cardStyle({{ $index }})"
                            @click="onCardClick({{ $index }}, () => modal = {{ Illuminate\Support\Js::from($data) }})"
                            class="coverflow-card absolute left-1/2 top-0 w-72 cursor-grab overflow-hidden rounded-panel border border-linen-200 bg-white text-left shadow-panel transition-[transform,opacity] duration-500 ease-pib active:cursor-grabbing">
                        <div class="aspect-[16/10] w-full overflow-hidden bg-linen-100">
                            <img src="{{ $image }}" alt="{{ $event->title }}" loading="lazy" draggable="false"
                                 class="pointer-events-none h-full w-full object-contain p-1">
                        </div>
                        <div class="p-5">
                            <span class="text-[11px] font-semibold uppercase tracking-[0.2em] text-ouro-700">@lang('site.events.eyebrow')</span>
                            <h3 class="mt-2 line-clamp-2 font-display text-xl leading-tight text-onyx-950">{{ $event->title }}</h3>
                            @if ($event->subtitle)
                                <p class="mt-1 line-clamp-1 text-sm text-onyx-600">{{ $event->subtitle }}</p>
                            @endif
                        </div>
                    </button>
                @endforeach
            </div>

            @if ($events->count() > 1)
                <button type="button" @click="prev(); scheduleResume()" aria-label="@lang('site.a11y.prev')"
                        class="absolute left-1 top-1/2 z-20 grid h-10 w-10 -translate-y-1/2 place-items-center rounded-pill border border-linen-300 bg-white/90 text-onyx-800 shadow-card transition duration-200 ease-pib hover:border-ouro-500 hover:text-ouro-700 active:scale-95">&lsaquo;</button>
                <button type="button" @click="next(); scheduleResume()" aria-label="@lang('site.a11y.next')"
                        class="absolute right-1 top-1/2 z-20 grid h-10 w-10 -translate-y-1/2 place-items-center rounded-pill border border-linen-300 bg-white/90 text-onyx-800 shadow-card transition duration-200 ease-pib hover:border-ouro-500 hover:text-ouro-700 active:scale-95">&rsaquo;</button>
            @endif
        </div>

        {{-- Modal de detalhes --}}
        <div x-show="modal" x-cloak @keydown.escape.window="modal = null"
             class="fixed inset-0 z-50 flex items-center justify-center bg-onyx-950/70 p-4 backdrop-blur-sm">
            <div @click.outside="modal = null"
                 class="modal-tall w-full max-w-2xl overflow-y-auto rounded-panel border border-linen-200 bg-white shadow-2xl">
                <template x-if="modal && modal.image">
                    <img :src="modal.image" loading="lazy" decoding="async" class="h-64 w-full bg-linen-100 object-contain p-2" alt="">
                </template>
                <div class="p-6 md:p-8">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <span class="text-xs font-semibold uppercase tracking-[0.2em] text-ouro-700">@lang('site.events.eyebrow')</span>
                            <h3 class="mt-3 font-display text-3xl leading-tight text-onyx-950" x-text="modal && modal.title"></h3>
                        </div>
                        <button type="button" @click="modal = null" aria-label="@lang('site.a11y.close')"
                                class="grid h-10 w-10 shrink-0 place-items-center rounded-pill border border-linen-200 text-xl text-onyx-700 transition duration-200 ease-pib hover:border-ouro-500 hover:text-ouro-700 active:scale-95">
                            &times;
                        </button>
                    </div>
                    <p class="mt-3 text-lg text-onyx-600" x-show="modal && modal.subtitle" x-text="modal && modal.subtitle"></p>
                    <p class="mt-5 whitespace-pre-line leading-relaxed text-onyx-700" x-show="modal && modal.description" x-text="modal && modal.description"></p>
                </div>
            </div>
        </div>
    </section>
@endif
