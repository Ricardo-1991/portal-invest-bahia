@if ($events->isNotEmpty())
    <section x-data="{ modal: null }" class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
        <div class="mb-8 flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
            <div>
                <span class="text-xs font-semibold uppercase tracking-[0.24em] text-ouro-700">@lang('site.events.eyebrow')</span>
                <h2 class="mt-3 font-display text-3xl text-onyx-950 md:text-4xl" data-reveal>@lang('site.events.title')</h2>
            </div>
            <p class="max-w-xl text-sm leading-relaxed text-onyx-600">
                @lang('site.trust.text')
            </p>
        </div>

        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            @foreach ($events as $event)
                @php
                    $image = $event->imageUrl('thumb') ?: asset('images/property-placeholder.png');
                    $modalImage = $event->imageUrl() ?: $image;
                    $data = [
                        'title' => $event->title,
                        'subtitle' => $event->subtitle,
                        'description' => $event->description,
                        'image' => $modalImage,
                    ];
                @endphp

                <button type="button"
                        @click="modal = {{ Illuminate\Support\Js::from($data) }}"
                        class="group overflow-hidden rounded-[2rem] border border-linen-200 bg-white text-left shadow-sm transition duration-300 hover:-translate-y-1 hover:shadow-xl">
                    <div class="aspect-[16/10] w-full overflow-hidden bg-linen-100">
                        <img src="{{ $image }}" alt="{{ $event->title }}" loading="lazy"
                             class="h-full w-full object-cover transition duration-700 group-hover:scale-105">
                    </div>
                    <div class="p-5">
                        <span class="text-xs font-semibold uppercase tracking-[0.2em] text-ouro-700">@lang('site.events.eyebrow')</span>
                        <h3 class="mt-3 line-clamp-2 font-display text-2xl leading-tight text-onyx-950">{{ $event->title }}</h3>
                        @if ($event->subtitle)
                            <p class="mt-2 line-clamp-2 text-sm leading-relaxed text-onyx-600">{{ $event->subtitle }}</p>
                        @endif
                    </div>
                </button>
            @endforeach
        </div>

        <div x-show="modal" x-cloak @keydown.escape.window="modal = null"
             class="fixed inset-0 z-50 flex items-center justify-center bg-onyx-950/70 p-4 backdrop-blur-sm">
            <div @click.outside="modal = null"
                 class="max-h-[85vh] w-full max-w-2xl overflow-y-auto rounded-[2rem] border border-linen-200 bg-white shadow-2xl">
                <template x-if="modal && modal.image">
                    <img :src="modal.image" class="h-64 w-full object-cover" alt="">
                </template>
                <div class="p-6 md:p-8">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <span class="text-xs font-semibold uppercase tracking-[0.2em] text-ouro-700">@lang('site.events.eyebrow')</span>
                            <h3 class="mt-3 font-display text-3xl leading-tight text-onyx-950" x-text="modal && modal.title"></h3>
                        </div>
                        <button type="button" @click="modal = null" aria-label="Fechar"
                                class="grid h-10 w-10 shrink-0 place-items-center rounded-full border border-linen-200 text-xl text-onyx-700 transition hover:border-ouro-500 hover:text-ouro-700">
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
