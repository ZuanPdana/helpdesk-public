@extends('layouts.user')

@section('title', $ticket->ticket_number)
@section('page_title', __('tickets.ticket_detail'))
@section('breadcrumb')
    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
    <a href="{{ route('user.tickets.index') }}" class="hover:text-gray-700">{{ __('tickets.my_tickets') }}</a>
    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
    <span class="text-gray-700">{{ $ticket->ticket_number }}</span>
@endsection

@section('content')
    <div class="max-w-5xl mx-auto space-y-6">

        {{-- Ticket header --}}
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2 mb-2">
                        <span class="text-sm font-mono font-medium text-primary-600">{{ $ticket->ticket_number }}</span>
                    </div>
                    <h1 class="text-xl font-bold text-gray-900">{{ $ticket->title }}</h1>
                    <p class="text-sm text-gray-500 mt-1">
                        {{ __('tickets.created_at_label') }} {{ $ticket->created_at->format('M d, Y \\at h:i A') }}
                        @if($ticket->updated_at->ne($ticket->created_at))
                            · {{ __('tickets.updated_label') }} {{ $ticket->updated_at->diffForHumans() }}
                        @endif
                    </p>
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    <x-priority-badge :priority="$ticket->priority" />
                    <x-status-badge :status="$ticket->status" />
                </div>
            </div>
        </div>

        {{-- Status timeline --}}
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <h2 class="text-base font-semibold text-gray-900 mb-5">{{ __('tickets.ticket_progress') }}</h2>
            <x-status-timeline :currentStatus="$ticket->status" />
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Left column: details --}}
            <div class="lg:col-span-2 space-y-6">

                {{-- Description --}}
                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <h2 class="text-base font-semibold text-gray-900 mb-3">{{ __('tickets.description_section') }}</h2>
                    <div class="text-sm text-gray-700 leading-relaxed whitespace-pre-wrap">{{ $ticket->description }}</div>
                </div>

                {{-- Evidence --}}
                @if($ticket->evidenceAttachments->isNotEmpty())
                    <div class="bg-white rounded-xl border border-gray-200 p-6">
                        <h2 class="text-base font-semibold text-gray-900 mb-4">{{ __('tickets.evidence_section') }}</h2>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                            @foreach($ticket->evidenceAttachments as $attachment)
                                @if($attachment->isImage())
                                    <a href="{{ asset('storage/' . $attachment->file_path) }}" target="_blank" class="group relative aspect-square rounded-lg overflow-hidden bg-gray-100 border border-gray-200 hover:border-primary-300 transition-colors">
                                        <img src="{{ asset('storage/' . $attachment->file_path) }}" alt="{{ $attachment->file_name }}" class="w-full h-full object-cover">
                                        <div class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition-colors flex items-center justify-center">
                                            <svg class="w-6 h-6 text-white opacity-0 group-hover:opacity-100 transition-opacity" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607zM10.5 7.5v6m3-3h-6" />
                                            </svg>
                                        </div>
                                    </a>
                                @else
                                    <a href="{{ asset('storage/' . $attachment->file_path) }}" target="_blank" class="flex items-center gap-3 p-3 rounded-lg border border-gray-200 hover:border-primary-300 hover:bg-gray-50 transition-colors">
                                        <div class="w-10 h-10 rounded-lg bg-gray-100 flex items-center justify-center shrink-0">
                                            <svg class="w-5 h-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                                            </svg>
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-sm font-medium text-gray-700 truncate">{{ $attachment->file_name }}</p>
                                            <p class="text-xs text-gray-400">{{ $attachment->humanFileSize() }}</p>
                                        </div>
                                    </a>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Resolution evidence --}}
                @if($ticket->resolutionAttachments->isNotEmpty())
                    <div class="bg-white rounded-xl border border-emerald-200 p-6">
                        <h2 class="text-base font-semibold text-gray-900 mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            {{ __('tickets.resolution_evidence') }}
                        </h2>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                            @foreach($ticket->resolutionAttachments as $attachment)
                                @if($attachment->isImage())
                                    <a href="{{ asset('storage/' . $attachment->file_path) }}" target="_blank" class="group relative aspect-square rounded-lg overflow-hidden bg-gray-100 border border-gray-200 hover:border-emerald-300 transition-colors">
                                        <img src="{{ asset('storage/' . $attachment->file_path) }}" alt="{{ $attachment->file_name }}" class="w-full h-full object-cover">
                                    </a>
                                @else
                                    <a href="{{ asset('storage/' . $attachment->file_path) }}" target="_blank" class="flex items-center gap-3 p-3 rounded-lg border border-gray-200 hover:border-emerald-300 hover:bg-gray-50 transition-colors">
                                        <div class="w-10 h-10 rounded-lg bg-emerald-50 flex items-center justify-center shrink-0">
                                            <svg class="w-5 h-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                                            </svg>
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-sm font-medium text-gray-700 truncate">{{ $attachment->file_name }}</p>
                                            <p class="text-xs text-gray-400">{{ $attachment->humanFileSize() }}</p>
                                        </div>
                                    </a>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Comments --}}
                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <h2 class="text-base font-semibold text-gray-900 mb-4">
                        {{ __('tickets.comments_section') }}
                        @if($ticket->comments->isNotEmpty())
                            <span class="text-sm font-normal text-gray-400">({{ $ticket->comments->count() }})</span>
                        @endif
                    </h2>

                    @if($ticket->comments->isNotEmpty())
                        <div class="space-y-4 mb-6">
                            @foreach($ticket->comments->sortBy('created_at') as $comment)
                                <div class="flex gap-3">
                                    <div class="w-8 h-8 rounded-full {{ $comment->user_id === auth()->id() ? 'bg-primary-100 text-primary-700' : 'bg-amber-100 text-amber-700' }} flex items-center justify-center text-xs font-bold shrink-0">
                                        {{ $comment->user->initials() }}
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-2 mb-1">
                                            <span class="text-sm font-semibold text-gray-900">{{ $comment->user->name }}</span>
                                            <span class="text-xs text-gray-400">{{ $comment->user->role->label() }}</span>
                                            <span class="text-xs text-gray-400">·</span>
                                            <time class="text-xs text-gray-400" datetime="{{ $comment->created_at->toIso8601String() }}">
                                                {{ $comment->created_at->format('M d, Y h:i A') }}
                                            </time>
                                        </div>
                                        <div class="text-sm text-gray-700 bg-gray-50 rounded-lg px-4 py-3 whitespace-pre-wrap">{{ $comment->body }}</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-gray-400 mb-6">{{ __('tickets.no_comments') }}</p>
                    @endif

                    {{-- Add comment form --}}
                    @can('addComment', $ticket)
                        <form method="POST" action="{{ route('user.tickets.comments.store', $ticket) }}" x-data="{ body: '', submitting: false }" @submit="submitting = true">
                            @csrf
                            <div class="border-t border-gray-100 pt-4">
                                <label for="comment-body" class="sr-only">{{ __('tickets.write_comment') }}</label>
                                <textarea
                                    id="comment-body"
                                    name="body"
                                    aria-invalid="{{ $errors->has('body') ? 'true' : 'false' }}"
                                    rows="3"
                                    required
                                    x-model="body"
                                    class="field-border block w-full rounded-lg px-3.5 py-2.5 text-sm text-gray-900 shadow-sm placeholder:text-gray-400 focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 focus:outline-none transition resize-y"
                                    placeholder="{{ __('tickets.write_comment_placeholder') }}"
                                ></textarea>
                                @error('body')
                                    <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <div class="flex justify-end mt-3">
                                    <button
                                        type="submit"
                                        :disabled="!body.trim() || submitting"
                                        class="rounded-lg bg-primary-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
                                    >
                                        <svg x-show="submitting" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                                        {{ __('tickets.send_comment') }}
                                    </button>
                                </div>
                            </div>
                        </form>
                    @endcan
                </div>

                {{-- Activity / History --}}
                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <h2 class="text-base font-semibold text-gray-900 mb-4">{{ __('tickets.activity_history') }}</h2>

                    @if($ticket->histories->isNotEmpty())
                        <div class="relative">
                            {{-- Timeline line --}}
                            <div class="absolute left-4 top-0 bottom-0 w-px bg-gray-200"></div>

                            <div class="space-y-6">
                                @foreach($ticket->histories->sortByDesc('created_at') as $history)
                                    <div class="relative flex gap-4 pl-10">
                                        {{-- Dot --}}
                                        <div class="absolute left-2.5 top-1 w-3 h-3 rounded-full {{ $history->action === 'created' ? 'bg-primary-500' : ($history->action === 'status_changed' ? 'bg-amber-400' : 'bg-gray-300') }} ring-4 ring-white"></div>

                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm text-gray-900">
                                                {{ $history->description }}
                                            </p>
                                            @if($history->old_value && $history->new_value && $history->action === 'status_changed')
                                                <p class="text-xs text-gray-500 mt-0.5">
                                                    {{ ucfirst(str_replace('_', ' ', $history->old_value)) }} → {{ ucfirst(str_replace('_', ' ', $history->new_value)) }}
                                                </p>
                                            @endif
                                            <div class="flex items-center gap-2 mt-1">
                                                @if($history->user)
                                                    <span class="text-xs text-gray-400">{{ __('tickets.by_prefix') }} {{ $history->user->name }}</span>
                                                    <span class="text-xs text-gray-300">·</span>
                                                @endif
                                                <time class="text-xs text-gray-400" datetime="{{ $history->created_at->toIso8601String() }}">
                                                    {{ $history->created_at->format('M d, Y h:i A') }}
                                                </time>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <p class="text-sm text-gray-400">{{ __('tickets.no_activity') }}</p>
                    @endif
                </div>
            </div>

            {{-- Right column: sidebar info --}}
            <div class="space-y-6">
                {{-- Ticket info --}}
                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <h2 class="text-base font-semibold text-gray-900 mb-4">{{ __('tickets.ticket_info_section') }}</h2>
                    <dl class="space-y-4">
                        <div>
                            <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('tickets.status_label') }}</dt>
                            <dd class="mt-1"><x-status-badge :status="$ticket->status" /></dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('tickets.priority_label_dt') }}</dt>
                            <dd class="mt-1"><x-priority-badge :priority="$ticket->priority" /></dd>
                        </div>
                        @if($ticket->location)
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('tickets.location_label_dt') }}</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $ticket->location }}</dd>
                            </div>
                        @endif
                        <div>
                            <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('tickets.created_label_dt') }}</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $ticket->created_at->format('M d, Y h:i A') }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('tickets.last_updated_label') }}</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $ticket->updated_at->diffForHumans() }}</dd>
                        </div>
                        @if($ticket->resolved_at)
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('tickets.resolved_at_label') }}</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $ticket->resolved_at->format('M d, Y h:i A') }}</dd>
                            </div>
                        @endif
                        @if($ticket->closed_at)
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('tickets.closed_at_label') }}</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $ticket->closed_at->format('M d, Y h:i A') }}</dd>
                            </div>
                        @endif
                    </dl>

                    {{-- Close button --}}
                    @if(in_array($ticket->status, [\App\Enums\TicketStatus::Open, \App\Enums\TicketStatus::Resolved]))
                        <div class="mt-4 pt-4 border-t border-gray-100">
                            <form method="POST" action="{{ route('user.tickets.close', $ticket) }}" x-data="{ open: false }">
                                @csrf
                                <button type="button" @click="open = true" class="w-full rounded-lg bg-red-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-red-700 transition-colors">
                                    {{ __('tickets.close_ticket') }}
                                </button>

                                <template x-if="open">
                                    <div x-show="open" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4" @click.self="open = false">
                                        <div class="bg-white rounded-lg shadow-lg max-w-sm w-full p-6">
                                            <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ __('tickets.close_ticket_confirm_title') }}</h3>
                                            <p class="text-sm text-gray-600 mb-4">{{ __('tickets.close_ticket_confirm_desc') }}</p>
                                            <div class="flex gap-3">
                                                <button type="button" @click="open = false" class="flex-1 rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                                                    {{ __('common.cancel') }}
                                                </button>
                                                <button type="submit" class="flex-1 rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700 transition-colors">
                                                    {{ __('tickets.close_ticket') }}
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </form>
                        </div>
                    @endif
                </div>

                {{-- Assigned Technician --}}
                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <h2 class="text-base font-semibold text-gray-900 mb-4">{{ __('tickets.assigned_technician_section') }}</h2>
                    @if($ticket->assignee)
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-amber-100 flex items-center justify-center text-sm font-bold text-amber-700 shrink-0">
                                {{ $ticket->assignee->initials() }}
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-900">{{ $ticket->assignee->name }}</p>
                                <p class="text-xs text-gray-500">{{ $ticket->assignee->email }}</p>
                                @if($ticket->assignee->department)
                                    <p class="text-xs text-gray-400">{{ $ticket->assignee->department }}</p>
                                @endif
                            </div>
                        </div>
                    @else
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">{{ __('tickets.not_yet_assigned') }}</p>
                                <p class="text-xs text-gray-400">{{ __('tickets.technician_assigned_shortly') }}</p>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Submitted by --}}
                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <h2 class="text-base font-semibold text-gray-900 mb-4">{{ __('tickets.submitted_by') }}</h2>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-primary-100 flex items-center justify-center text-sm font-bold text-primary-700 shrink-0">
                            {{ $ticket->user->initials() }}
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-900">{{ $ticket->user->name }}</p>
                            <p class="text-xs text-gray-500">{{ $ticket->user->email }}</p>
                            @if($ticket->user->department)
                                <p class="text-xs text-gray-400">{{ $ticket->user->department }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
