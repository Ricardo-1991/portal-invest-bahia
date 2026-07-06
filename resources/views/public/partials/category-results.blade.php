@if ($listings->isEmpty())
    <p class="mt-12 text-center text-onyx-400">@lang('site.listing.no_results')</p>
@else
    <div class="mt-8 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
        @foreach ($listings as $listing)
            @include('public.partials.listing-card', ['listing' => $listing])
        @endforeach
    </div>

    <div class="mt-8">{{ $listings->links() }}</div>
@endif
