@if ($events->isNotEmpty())
    <section x-data="{ modal: null }" class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
        <h2 class="mb-10 text-center font-display text-2xl text-onyx-50" data-reveal>@lang('site.events.title')</h2>

        {{-- Carrossel coverflow: autoplay em loop, arraste para navegar, clique abre detalhes --}}
        <div class="relative mx-auto max-w-3xl overflow-hidden px-4" style="touch-action: pan-y;"
             x-data="coverflow({{ $events->count() }})"
             @pointerdown="dragStart($event)"
             @pointermove="dragMove($event)"
             @pointerup="dragEnd($event)"
             @pointerleave="dragging && dragEnd($event)">

            <div class="relative h-72 select-none" style="perspective: 1200px;">
                @foreach ($events as $index => $event)
                    @php
                        $data = [
                            'title' => $event->title,
                            'subtitle' => $event->subtitle,
                            'description' => $event->description,
                            'image' => $event->imageUrl(),
                        ];
                    @endphp
                    <button type="button"
                            :style="cardStyle({{ $index }})"
                            @click="onCardClick({{ $index }}, () => modal = {{ Illuminate\Support\Js::from($data) }})"
                            class="coverflow-card absolute left-1/2 top-0 w-64 cursor-grab overflow-hidden rounded-2xl border border-onyx-700 bg-onyx-800 text-left transition-[transform,opacity] duration-500 ease-out active:cursor-grabbing">
                        <div class="aspect-[16/10] w-full bg-onyx-900">
                            @if ($url = $event->imageUrl('thumb'))
                                <img src="{{ $url }}" alt="{{ $event->title }}" loading="lazy" draggable="false"
                                     class="pointer-events-none h-full w-full object-cover">
                            @else
                                <div class="grid h-full w-full place-items-center">
                                    <img src="{{ asset('images/logo.jpeg') }}" alt="" draggable="false"
                                         class="pointer-events-none h-10 w-10 rounded-lg object-cover opacity-40">
                                </div>
                            @endif
                        </div>
                        <div class="p-4">
                            <h3 class="line-clamp-2 font-semibold text-onyx-50">{{ $event->title }}</h3>
                            @if ($event->subtitle)
                                <p class="line-clamp-1 text-sm text-onyx-400">{{ $event->subtitle }}</p>
                            @endif
                        </div>
                    </button>
                @endforeach
            </div>

            @if ($events->count() > 1)
                <button type="button" @click="prev(); scheduleResume()" aria-label="Anterior"
                        class="absolute left-1 top-1/2 z-20 grid h-9 w-9 -translate-y-1/2 place-items-center rounded-lg border border-onyx-700 bg-onyx-900/80 text-onyx-100 transition hover:border-ouro-500 hover:text-ouro-400">‹</button>
                <button type="button" @click="next(); scheduleResume()" aria-label="Próximo"
                        class="absolute right-1 top-1/2 z-20 grid h-9 w-9 -translate-y-1/2 place-items-center rounded-lg border border-onyx-700 bg-onyx-900/80 text-onyx-100 transition hover:border-ouro-500 hover:text-ouro-400">›</button>
            @endif
        </div>

        {{-- Modal de detalhes --}}
        <div x-show="modal" x-cloak @keydown.escape.window="modal = null"
             class="fixed inset-0 z-50 flex items-center justify-center bg-onyx-950/80 p-4">
            <div @click.outside="modal = null"
                 class="max-h-[85vh] w-full max-w-lg overflow-y-auto rounded-2xl border border-onyx-700 bg-onyx-900 shadow-2xl">
                <template x-if="modal && modal.image">
                    <img :src="modal && modal.image" class="h-56 w-full object-cover" alt="">
                </template>
                <div class="p-6">
                    <h3 class="font-display text-xl text-onyx-50" x-text="modal && modal.title"></h3>
                    <p class="mt-1 text-onyx-400" x-text="modal && modal.subtitle"></p>
                    <p class="mt-4 whitespace-pre-line text-onyx-200" x-text="modal && modal.description"></p>
                    <button @click="modal = null" aria-label="Fechar"
                            class="mt-6 rounded-lg border border-ouro-500 px-4 py-2 font-semibold text-ouro-400 transition hover:bg-ouro-400 hover:text-onyx-950">
                        &times;
                    </button>
                </div>
            </div>
        </div>
    </section>
@endif
