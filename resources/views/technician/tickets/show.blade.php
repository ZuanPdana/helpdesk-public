@extends('layouts.technician')

@section('title', $ticket->ticket_number)
@section('page_title', $ticket->title)

@section('content')
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl border border-gray-200 p-6 mb-6">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <div class="flex items-center gap-3">
                            <h2 class="text-lg font-semibold text-gray-900">{{ $ticket->ticket_number }}</h2>
                            <x-status-badge :status="$ticket->status" />
                            <x-priority-badge :priority="$ticket->priority" />
                        </div>

                        <p class="mt-3 text-sm text-gray-700">{{ $ticket->description }}</p>

                        <div class="mt-4 text-xs text-gray-500">
                            <div>Requester: {{ $ticket->user?->name ?? '—' }}</div>
                            <div>Location: {{ $ticket->location ?? '—' }}</div>
                            <div>Created: {{ $ticket->created_at->format('M d, Y H:i') }}</div>
                            <div>Assigned: {{ $ticket->assignee?->name ?? 'Unassigned' }}</div>
                        </div>
                    </div>
                </div>

                {{-- Attachments / User evidence --}}
                <div class="mt-6">
                    <h3 class="text-sm font-semibold text-gray-900 mb-2">User Evidence</h3>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                        @forelse($ticket->attachments as $att)
                            <div class="border rounded-lg p-2 text-center">
                                @if($att->isImage())
                                    <img src="{{ asset('storage/'.$att->file_path) }}" alt="{{ $att->file_name }}" class="max-h-28 mx-auto rounded" />
                                @else
                                    <div class="text-xs font-medium">{{ $att->file_name }}</div>
                                @endif
                                <div class="text-xs text-gray-500 mt-1">{{ $att->humanFileSize() }}</div>
                                <a href="{{ asset('storage/'.$att->file_path) }}" target="_blank" class="mt-2 inline-block text-sm text-primary-600">Open</a>
                            </div>
                        @empty
                            <div class="text-sm text-gray-500">No user evidence uploaded.</div>
                        @endforelse
                    </div>
                </div>

                {{-- Conversation / Comments --}}
                <div class="mt-6">
                    <h3 class="text-sm font-semibold text-gray-900 mb-2">Conversation</h3>
                    <div class="space-y-4">
                        @foreach($ticket->comments as $comment)
                            <div class="bg-gray-50 rounded-lg p-3">
                                <div class="flex items-start justify-between">
                                    <div>
                                        <div class="text-sm font-medium">{{ $comment->user?->name ?? 'Unknown' }}</div>
                                        <div class="text-xs text-gray-500">{{ $comment->created_at->diffForHumans() }}</div>
                                    </div>
                                </div>
                                <p class="mt-2 text-sm text-gray-700">{{ $comment->body }}</p>
                            </div>
                        @endforeach
                    </div>

                    <form action="{{ route('technician.tickets.comments.store', $ticket) }}" method="POST" class="mt-4">
                        @csrf
                        <textarea name="body" rows="3" class="w-full rounded-lg border border-gray-300 p-3 text-sm" placeholder="Add a comment..."></textarea>
                        @error('body')<div class="text-red-500 text-sm mt-1">{{ $message }}</div>@enderror
                        <div class="mt-2 flex items-center gap-2">
                            <button class="rounded-lg bg-primary-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-primary-700">Send Comment</button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Activity / History --}}
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h3 class="text-sm font-semibold text-gray-900 mb-4">Activity</h3>
                <ul class="space-y-3">
                    @foreach($ticket->histories as $history)
                        <li class="text-sm text-gray-700">
                            <div class="text-xs text-gray-500">{{ $history->created_at->format('M d, Y H:i') }} — {{ $history->user?->name ?? 'System' }}</div>
                            <div class="mt-1">{{ $history->description }}</div>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>

        <aside class="space-y-6">
            <div class="bg-white rounded-xl border border-gray-200 p-4">
                <h4 class="text-sm font-semibold text-gray-900">Action</h4>
                <div class="mt-3">
                    <div class="mb-3 text-xs text-gray-500">Current Status</div>
                    <div class="mb-4"><x-status-badge :status="$ticket->status" /></div>

                    @can('selfAssign', $ticket)
                        <form action="{{ route('technician.tickets.assign-self', $ticket) }}" method="POST" class="mb-4">
                            @csrf
                            <button type="submit" class="w-full rounded-lg bg-primary-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-primary-700">{{ __('tickets.self_assign') }}</button>
                        </form>
                    @elseif($ticket->assigned_to === auth()->id())
                        <div class="mb-4 rounded-lg bg-indigo-50 px-3 py-2 text-sm font-medium text-indigo-700">{{ __('tickets.self_assigned') }}</div>
                    @endif

                    @can('addComment', $ticket)
                        <form action="{{ route('technician.tickets.status.update', $ticket) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <select name="status" class="w-full rounded-lg border border-gray-300 px-3.5 py-2.5 text-sm">
                                @php
                                    $technicianStatuses = [
                                        \App\Enums\TicketStatus::InProgress,
                                        \App\Enums\TicketStatus::Resolved,
                                    ];
                                @endphp
                                @foreach($technicianStatuses as $status)
                                    <option value="{{ $status->value }}" {{ $ticket->status->value === $status->value ? 'selected' : '' }}>{{ $status->label() }}</option>
                                @endforeach
                            </select>
                            <button type="submit" class="mt-3 w-full rounded-lg bg-primary-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-primary-700">Update Status</button>
                        </form>
                    @endcan
                </div>

                <div class="mt-4">
                    <h4 class="text-sm font-semibold text-gray-900">Upload Repair Evidence</h4>
                    <form action="{{ route('technician.tickets.evidence.upload', $ticket) }}" method="POST" enctype="multipart/form-data" class="mt-3" x-data="evidenceUploader()">
                        @csrf

                        <div x-on:dragover.prevent="isDragging = true" x-on:dragleave.prevent="isDragging = false" x-on:drop.prevent="handleDrop($event)"
                             :class="isDragging ? 'border-emerald-500 bg-emerald-50' : 'border-gray-200'"
                             class="w-full cursor-pointer rounded-lg border-2 border-dashed px-4 py-6 text-center transition mb-3">

                            <input x-ref="fileInput" type="file" name="evidence[]" multiple accept="image/*,application/pdf" class="hidden" @change="handleFiles($event)" />

                            <div class="flex flex-col items-center justify-center gap-2">
                                <svg class="w-10 h-10 mx-auto text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v8m0 0l3-3m-3 3l-3-3M4.5 19.5a4.5 4.5 0 014.5-4.5h6a4.5 4.5 0 110 9H9a4.5 4.5 0 01-4.5-4.5z" />
                                </svg>
                                <div class="text-sm font-medium text-gray-700">Drag &amp; drop evidence here, or <button type="button" class="text-emerald-600 underline" @click.prevent="$refs.fileInput.click()">browse</button></div>
                                <div class="text-xs text-gray-400">PNG, JPG, JPEG, WEBP, PDF (max 5MB each)</div>
                            </div>

                            <template x-if="files.length">
                                <div class="mt-4 text-sm text-gray-600">
                                    <div class="font-medium">Selected files (max 5)</div>
                                </div>
                            </template>
                        </div>

                        <div x-show="files.length > 0" class="mt-2 space-y-2">
                            <template x-for="(file, index) in files" :key="index">
                                <div class="flex items-center gap-3 px-3 py-2 bg-gray-50 rounded-lg border border-gray-100">
                                    <template x-if="file.preview">
                                        <img :src="file.preview" class="w-12 h-12 rounded object-cover shrink-0" alt="Preview">
                                    </template>
                                    <template x-if="!file.preview">
                                        <div class="w-12 h-12 rounded bg-gray-200 flex items-center justify-center shrink-0">
                                            <svg class="w-6 h-6 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5" />
                                            </svg>
                                        </div>
                                    </template>

                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm text-gray-700 truncate" x-text="file.name"></p>
                                        <p class="text-xs text-gray-400" x-text="file.size"></p>
                                    </div>

                                    <div class="flex items-center gap-2">
                                        <button type="button" @click="removeFile(index)" class="text-gray-400 hover:text-red-500 transition-colors shrink-0">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                                        </button>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <textarea name="notes" rows="3" class="w-full rounded-lg border border-gray-300 p-2 mt-3" placeholder="Notes (optional)"></textarea>
                        @error('evidence')<div class="text-red-500 text-sm mt-1">{{ $message }}</div>@enderror

                        <div class="mt-3">
                            <button type="submit" class="mt-2 w-full rounded-lg bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white">Upload Evidence</button>
                        </div>

                        <script>
                            function evidenceUploader() {
                                return {
                                    files: [],
                                    isDragging: false,
                                    maxFiles: 5,
                                    maxSizeMB: 5,
                                    formatBytes(bytes) {
                                        if (bytes === 0) return '0 B';
                                        const k = 1024;
                                        const sizes = ['B', 'KB', 'MB', 'GB'];
                                        const i = Math.floor(Math.log(bytes) / Math.log(k));
                                        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
                                    },
                                    handleFiles(e) {
                                        const inputFiles = e.target.files;
                                        this.addFiles(inputFiles);
                                    },
                                    addFiles(fileList) {
                                        for (let i = 0; i < fileList.length; i++) {
                                            if (this.files.length >= this.maxFiles) break;
                                            const file = fileList[i];
                                            if (!file) continue;
                                            if (!file.type.startsWith('image/') && file.type !== 'application/pdf') {
                                                alert('Only images and PDF files are allowed.');
                                                continue;
                                            }
                                            if (file.size > this.maxSizeMB * 1024 * 1024) {
                                                alert('File "' + file.name + '" exceeds the max size of ' + this.maxSizeMB + 'MB.');
                                                continue;
                                            }
                                            const preview = file.type.startsWith('image/') ? URL.createObjectURL(file) : null;
                                            this.files.push({ name: file.name, size: this.formatBytes(file.size), preview: preview });
                                        }
                                        // sync the actual FileList into the hidden input
                                        const dt = new DataTransfer();
                                        const input = this.$refs.fileInput;
                                        // if there were existing files in input, add them first
                                        const existing = input.files;
                                        for (let i = 0; i < existing.length; i++) dt.items.add(existing[i]);
                                        // then add new ones from fileList up to maxFiles
                                        for (let i = 0; i < fileList.length; i++) {
                                            if (dt.files.length >= this.maxFiles) break;
                                            dt.items.add(fileList[i]);
                                        }
                                        input.files = dt.files;
                                    },
                                    handleDrop(e) {
                                        this.isDragging = false;
                                        const dtFiles = e.dataTransfer.files;
                                        this.addFiles(dtFiles);
                                        // set input files same as addFiles does
                                        // handled in addFiles
                                    },
                                    removeFile(index) {
                                        // remove preview
                                        if (this.files[index]?.preview) URL.revokeObjectURL(this.files[index].preview);
                                        this.files.splice(index, 1);

                                        // rebuild FileList from remaining files in input
                                        const input = this.$refs.fileInput;
                                        const oldFiles = input.files;
                                        const dt = new DataTransfer();
                                        for (let i = 0; i < oldFiles.length; i++) {
                                            if (i === index) continue; // naive: assumes order matches
                                            dt.items.add(oldFiles[i]);
                                        }
                                        input.files = dt.files;
                                    }
                                }
                            }
                        </script>
                    </form>
                </div>
            </div>
        </aside>
    </div>
@endsection
