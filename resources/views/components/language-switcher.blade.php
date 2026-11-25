@php
    $currentLocale = app()->getLocale();
    $locales = [
        'id' => ['label' => 'Bahasa Indonesia', 'flag' => '🇮🇩', 'short' => 'ID'],
        'en' => ['label' => 'English', 'flag' => '🇬🇧', 'short' => 'EN'],
    ];
    $current = $locales[$currentLocale] ?? $locales['id'];
@endphp

<div class="relative" x-data="{ langOpen: false }" @click.outside="langOpen = false">
    <button
        @click="langOpen = !langOpen"
        type="button"
        class="flex items-center gap-1.5 rounded-lg px-2.5 py-2 text-sm text-gray-500 transition-colors hover:bg-gray-100 hover:text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary-500/20"
        :aria-expanded="langOpen"
        aria-label="{{ __('common.change_language') }}"
    >
        <span class="text-base leading-none" aria-hidden="true">🌐</span>
        <span class="hidden text-xs font-semibold sm:inline">{{ $current['short'] }}</span>
        <svg class="h-3.5 w-3.5 transition-transform" :class="langOpen ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
        </svg>
    </button>

    <div
        x-show="langOpen"
        x-cloak
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="absolute right-0 z-50 mt-2 w-44 overflow-hidden rounded-xl border border-gray-200 bg-white shadow-lg"
    >
        <div class="p-1">
            @foreach($locales as $code => $info)
                <form method="POST" action="{{ route('language.switch') }}">
                    @csrf
                    <input type="hidden" name="locale" value="{{ $code }}">
                    <button
                        type="submit"
                        class="flex w-full items-center gap-2.5 rounded-lg px-3 py-2 text-sm transition-colors hover:bg-gray-50 {{ $currentLocale === $code ? 'font-semibold text-primary-600' : 'text-gray-700' }}"
                        aria-current="{{ $currentLocale === $code ? 'true' : 'false' }}"
                    >
                        <span class="text-base leading-none">{{ $info['flag'] }}</span>
                        <span class="flex-1 text-left">{{ $info['label'] }}</span>
                        @if($currentLocale === $code)
                            <svg class="h-3.5 w-3.5 shrink-0 text-primary-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                        @endif
                    </button>
                </form>
            @endforeach
        </div>
    </div>
</div>
