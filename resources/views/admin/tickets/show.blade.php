@extends('layouts.admin')

@section('title', $ticket->ticket_number)
@section('page_title', __('tickets.ticket_detail'))
@section('breadcrumb')
    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
    <a href="{{ route('admin.tickets.index') }}" class="hover:text-gray-700">{{ __('tickets.tickets') }}</a>
    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
    <span class="text-gray-700">{{ $ticket->ticket_number }}</span>
@endsection

@section('content')
    <div class="space-y-6">
        <div class="rounded-xl border border-gray-200 bg-white p-6">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                <div>
                    <p class="text-sm font-mono font-medium text-primary-600">{{ $ticket->ticket_number }}</p>
                    <h1 class="mt-1 text-xl font-bold text-gray-900">{{ $ticket->title }}</h1>
                    <p class="mt-2 text-sm text-gray-500">
                        Created {{ $ticket->created_at->format('M d, Y \a\t h:i A') }}
                        @if($ticket->updated_at->ne($ticket->created_at))
                            · Updated {{ $ticket->updated_at->diffForHumans() }}
                        @endif
                    </p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <x-priority-badge :priority="$ticket->priority" />
                    <x-status-badge :status="$ticket->status" />
                    <a href="{{ route('admin.tickets.index') }}" class="rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm transition-colors hover:bg-gray-50">{{ __('common.back') }}</a>
                </div>
            </div>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-6">
            <h2 class="mb-5 text-base font-semibold text-gray-900">Status Workflow</h2>
            <x-status-timeline :currentStatus="$ticket->status" />
        </div>

        <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">
            <div class="space-y-6 xl:col-span-2">
                <div class="rounded-xl border border-gray-200 bg-white p-6">
                    <h2 class="mb-3 text-base font-semibold text-gray-900">{{ __('tickets.description_section') }}</h2>
                    <div class="whitespace-pre-wrap text-sm leading-relaxed text-gray-700">{{ $ticket->description }}</div>
                </div>

                <div class="rounded-xl border border-gray-200 bg-white p-6">
                    <h2 class="mb-4 text-base font-semibold text-gray-900">{{ __('tickets.evidence_section') }}</h2>
                    @if($ticket->attachments->isEmpty())
                        <p class="text-sm text-gray-500">{{ __('tickets.no_attachments') }}</p>
                    @else
                        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                            @foreach($ticket->attachments as $attachment)
                                <a href="{{ asset('storage/' . $attachment->file_path) }}" target="_blank" class="flex items-center gap-3 rounded-lg border border-gray-200 p-3 transition-colors hover:border-primary-300 hover:bg-gray-50">
                                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg {{ $attachment->type === 'resolution' ? 'bg-emerald-50 text-emerald-600' : 'bg-gray-100 text-gray-500' }}">
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" /></svg>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="truncate text-sm font-medium text-gray-700">{{ $attachment->file_name }}</p>
                                        <p class="text-xs text-gray-400">{{ ucfirst($attachment->type) }} · {{ $attachment->humanFileSize() }}</p>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="rounded-xl border border-gray-200 bg-white p-6">
                    <h2 class="mb-4 text-base font-semibold text-gray-900">{{ __('tickets.comments_section') }}</h2>
                    @if($ticket->comments->isEmpty())
                        <p class="text-sm text-gray-500">{{ __('tickets.no_comments') }}</p>
                    @else
                        <div class="space-y-4">
                            @foreach($ticket->comments->sortBy('created_at') as $comment)
                                <div class="flex gap-3">
                                    <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full {{ $comment->user->isTechnician() ? 'bg-amber-100 text-amber-700' : ($comment->user->isAdmin() ? 'bg-purple-100 text-purple-700' : 'bg-primary-100 text-primary-700') }} text-xs font-bold">
                                        {{ $comment->user->initials() }}
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <div class="mb-1 flex flex-wrap items-center gap-2">
                                            <span class="text-sm font-semibold text-gray-900">{{ $comment->user->name }}</span>
                                            <x-role-badge :role="$comment->user->role" />
                                            <time class="text-xs text-gray-400" datetime="{{ $comment->created_at->toIso8601String() }}">{{ $comment->created_at->format('M d, Y h:i A') }}</time>
                                        </div>
                                        <div class="whitespace-pre-wrap rounded-lg bg-gray-50 px-4 py-3 text-sm text-gray-700">{{ $comment->body }}</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="rounded-xl border border-gray-200 bg-white p-6">
                    <h2 class="mb-4 text-base font-semibold text-gray-900">{{ __('tickets.activity_history') }}</h2>
                    @if($ticket->histories->isEmpty())
                        <p class="text-sm text-gray-500">{{ __('tickets.no_activity') }}</p>
                    @else
                        <div class="relative">
                            <div class="absolute bottom-0 left-4 top-0 w-px bg-gray-200"></div>
                            <div class="space-y-6">
                                @foreach($ticket->histories->sortByDesc('created_at') as $history)
                                    <div class="relative flex gap-4 pl-10">
                                        <div class="absolute left-2.5 top-1 h-3 w-3 rounded-full {{ $history->action === 'status_changed' ? 'bg-amber-400' : 'bg-primary-500' }} ring-4 ring-white"></div>
                                        <div class="min-w-0 flex-1">
                                            <p class="text-sm text-gray-900">{{ $history->description }}</p>
                                            @if($history->old_value && $history->new_value && $history->action === 'status_changed')
                                                <p class="mt-0.5 text-xs text-gray-500">{{ ucfirst(str_replace('_', ' ', $history->old_value)) }} → {{ ucfirst(str_replace('_', ' ', $history->new_value)) }}</p>
                                            @endif
                                            <div class="mt-1 flex flex-wrap items-center gap-2 text-xs text-gray-400">
                                                @if($history->user)
                                                    <span>{{ $history->user->name }}</span>
                                                    <span class="text-gray-300">·</span>
                                                @endif
                                                <time datetime="{{ $history->created_at->toIso8601String() }}">{{ $history->created_at->format('M d, Y h:i A') }}</time>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <div class="space-y-6">
                <div class="rounded-xl border border-gray-200 bg-white p-6">
                    <h2 class="mb-4 text-base font-semibold text-gray-900">Ticket Information</h2>
                    <dl class="space-y-4">
                        <div>
                                <dt class="text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('tickets.requester') }}</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $ticket->user->name }}</dd>
                            <dd class="text-xs text-gray-500">{{ $ticket->user->email }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('tickets.priority_label_dt') }}</dt>
                            <dd class="mt-1"><x-priority-badge :priority="$ticket->priority" /></dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('tickets.status_label') }}</dt>
                            <dd class="mt-1"><x-status-badge :status="$ticket->status" /></dd>
                        </div>
                        @if($ticket->location)
                            <div>
                                <dt class="text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('tickets.location_label_dt') }}</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $ticket->location }}</dd>
                            </div>
                        @endif
                        <div>
                            <dt class="text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('tickets.created_at_label') }}</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $ticket->created_at->format('M d, Y h:i A') }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium uppercase tracking-wider text-gray-500">{{ __('tickets.last_updated_label') }}</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $ticket->updated_at->diffForHumans() }}</dd>
                        </div>
                    </dl>
                </div>

                <div class="rounded-xl border border-gray-200 bg-white p-6">
                    <h2 class="mb-4 text-base font-semibold text-gray-900">{{ __('tickets.assign_technician') }}</h2>
                    <form method="POST" action="{{ route('admin.tickets.assignment.update', $ticket) }}" class="space-y-4">
                        @csrf
                        @method('PATCH')
                        <div>
                            <label for="assigned_to" class="mb-1.5 block text-sm font-medium text-gray-700">{{ __('tickets.assigned_technician') }}</label>
                            <select id="assigned_to" name="assigned_to" class="block w-full rounded-lg border border-gray-300 px-3.5 py-2.5 text-sm text-gray-900 shadow-sm transition focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
                                <option value="">{{ __('tickets.unassigned') }}</option>
                                @foreach($technicians as $technician)
                                    <option value="{{ $technician->id }}" @selected((string) old('assigned_to', $ticket->assigned_to) === (string) $technician->id)>{{ $technician->name }} ({{ $technician->availabilityLabel() }})</option>
                                @endforeach
                            </select>
                            @error('assigned_to')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <button type="submit" class="w-full rounded-lg bg-primary-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-primary-700">{{ __('tickets.assign_technician') }}</button>
                    </form>
                </div>

                <div class="rounded-xl border border-gray-200 bg-white p-6">
                    <h2 class="mb-4 text-base font-semibold text-gray-900">{{ __('tickets.update_priority') }}</h2>
                    <form method="POST" action="{{ route('admin.tickets.priority.update', $ticket) }}" class="space-y-4">
                        @csrf
                        @method('PATCH')
                        <div>
                            <label for="priority" class="mb-1.5 block text-sm font-medium text-gray-700">{{ __('tickets.priority_label') }}</label>
                            <select id="priority" name="priority" class="block w-full rounded-lg border border-gray-300 px-3.5 py-2.5 text-sm text-gray-900 shadow-sm transition focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
                                @foreach($priorities as $priority)
                                    <option value="{{ $priority->value }}" @selected(old('priority', $ticket->priority->value) === $priority->value)>{{ $priority->label() }}</option>
                                @endforeach
                            </select>
                            @error('priority')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <button type="submit" class="w-full rounded-lg bg-gray-900 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-gray-800">{{ __('tickets.update_priority') }}</button>
                    </form>
                </div>

                <div class="rounded-xl border border-gray-200 bg-white p-6">
                    <h2 class="mb-4 text-base font-semibold text-gray-900">{{ __('tickets.update_status') }}</h2>
                    <form method="POST" action="{{ route('admin.tickets.status.update', $ticket) }}" class="space-y-4">
                        @csrf
                        @method('PATCH')
                        <div>
                            <label for="status" class="mb-1.5 block text-sm font-medium text-gray-700">{{ __('tickets.status_label') }}</label>
                            <select id="status" name="status" class="block w-full rounded-lg border border-gray-300 px-3.5 py-2.5 text-sm text-gray-900 shadow-sm transition focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
                                @foreach($statuses as $status)
                                    <option value="{{ $status->value }}" @selected(old('status', $ticket->status->value) === $status->value)>{{ $status->label() }}</option>
                                @endforeach
                            </select>
                            @error('status')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <button type="submit" class="w-full rounded-lg bg-gray-900 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-gray-800">{{ __('tickets.update_status') }}</button>
                    </form>
                </div>

                <div class="rounded-xl border border-gray-200 bg-white p-6">
                    <h2 class="mb-4 text-base font-semibold text-gray-900">Assigned Technician</h2>
                    @if($ticket->assignee)
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-amber-100 text-sm font-bold text-amber-700">{{ $ticket->assignee->initials() }}</div>
                            <div>
                                <p class="text-sm font-semibold text-gray-900">{{ $ticket->assignee->name }}</p>
                                <p class="text-xs text-gray-500">{{ $ticket->assignee->email }}</p>
                                <p class="text-xs text-gray-400">{{ $ticket->assignee->department ?: 'No department' }}</p>
                            </div>
                        </div>
                    @else
                        <p class="text-sm text-gray-500">No technician assigned yet.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
