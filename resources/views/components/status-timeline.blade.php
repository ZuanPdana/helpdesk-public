@props(['currentStatus'])

@php
    use App\Enums\TicketStatus;
    $steps = TicketStatus::orderedSteps();
    $currentIndex = $currentStatus->stepIndex();
@endphp

<div class="w-full">
    {{-- Desktop timeline --}}
    <div class="hidden sm:flex items-center justify-between relative">
        {{-- Background line --}}
        <div class="absolute top-5 left-0 right-0 h-0.5 bg-gray-200"></div>
        {{-- Progress line --}}
        <div class="absolute top-5 left-0 h-0.5 bg-primary-500 transition-all duration-500" style="width: {{ $currentIndex > 0 ? ($currentIndex / (count($steps) - 1)) * 100 : 0 }}%"></div>

        @foreach($steps as $index => $step)
            <div class="relative flex flex-col items-center z-10" style="width: {{ 100 / count($steps) }}%">
                @if($index < $currentIndex)
                    {{-- Completed --}}
                    <div class="w-10 h-10 rounded-full bg-primary-500 flex items-center justify-center shadow-sm">
                        <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                        </svg>
                    </div>
                @elseif($index === $currentIndex)
                    {{-- Current --}}
                    <div class="w-10 h-10 rounded-full bg-primary-500 flex items-center justify-center ring-4 ring-primary-100 shadow-sm">
                        <div class="w-3 h-3 rounded-full bg-white"></div>
                    </div>
                @else
                    {{-- Pending --}}
                    <div class="w-10 h-10 rounded-full bg-white border-2 border-gray-300 flex items-center justify-center">
                        <div class="w-2.5 h-2.5 rounded-full bg-gray-300"></div>
                    </div>
                @endif
                <p class="mt-2 text-xs font-medium {{ $index <= $currentIndex ? 'text-primary-700' : 'text-gray-400' }}">
                    {{ $step->label() }}
                </p>
            </div>
        @endforeach
    </div>

    {{-- Mobile timeline (vertical) --}}
    <div class="sm:hidden space-y-3">
        @foreach($steps as $index => $step)
            <div class="flex items-center gap-3">
                @if($index < $currentIndex)
                    <div class="w-8 h-8 rounded-full bg-primary-500 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                        </svg>
                    </div>
                @elseif($index === $currentIndex)
                    <div class="w-8 h-8 rounded-full bg-primary-500 flex items-center justify-center ring-4 ring-primary-100 shrink-0">
                        <div class="w-2.5 h-2.5 rounded-full bg-white"></div>
                    </div>
                @else
                    <div class="w-8 h-8 rounded-full bg-white border-2 border-gray-300 flex items-center justify-center shrink-0">
                        <div class="w-2 h-2 rounded-full bg-gray-300"></div>
                    </div>
                @endif
                <span class="text-sm font-medium {{ $index <= $currentIndex ? 'text-gray-900' : 'text-gray-400' }}">
                    {{ $step->label() }}
                </span>
            </div>
        @endforeach
    </div>
</div>
