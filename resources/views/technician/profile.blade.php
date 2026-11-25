@extends('layouts.technician')

@section('title', 'Profile')
@section('page_title', 'Profile')

@section('content')
    <div class="space-y-6">
        <div class="rounded-xl border border-gray-200 bg-white p-6">
            <form method="POST" action="{{ route('technician.profile.update') }}" enctype="multipart/form-data" class="grid grid-cols-1 gap-6 md:grid-cols-3">
                @csrf
                @method('PUT')

                <div class="md:col-span-1">
                    <div x-data="avatarUploader()" class="flex flex-col items-center">
                        <div class="relative">
                            <template x-if="previewUrl">
                                <img :src="previewUrl" alt="Preview" class="h-28 w-28 rounded-full object-cover mb-4" />
                            </template>

                            <div x-show="!previewUrl">
                                <x-user-avatar :user="$technician" size="profile" class="mb-4" />
                            </div>
                        </div>

                        <label class="text-sm text-gray-600 mb-2">Change profile picture</label>

                        <div x-on:dragover.prevent="isDragging = true" x-on:dragleave.prevent="isDragging = false" x-on:drop.prevent="handleDrop($event)"
                             :class="isDragging ? 'border-primary-500 bg-primary-50' : 'border-gray-200'"
                             class="w-full cursor-pointer rounded-lg border-2 border-dashed px-4 py-6 text-center transition">
                            <input x-ref="fileInput" type="file" name="avatar" accept="image/*" class="hidden" @change="handleFile($event)" />

                            <div class="flex flex-col items-center justify-center gap-2">
                                <svg class="w-8 h-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 16V8a2 2 0 012-2h6a2 2 0 012 2v8" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 12v6m0-6l-3 3m3-3l3 3" />
                                </svg>
                                <div class="text-sm font-medium text-gray-700">Drag &amp; drop an image, or <button type="button" class="text-primary-600 underline" @click.prevent="$refs.fileInput.click()">browse</button></div>
                                <div class="text-xs text-gray-400">PNG, JPG, JPEG, WEBP up to 2MB</div>
                            </div>

                            <template x-if="previewName">
                                <div class="mt-3 text-sm text-gray-700">Selected: <span class="font-medium" x-text="previewName"></span></div>
                            </template>
                        </div>

                        <div class="mt-3 flex items-center gap-2">
                            <button type="button" x-show="previewUrl" @click.prevent="removeFile()" class="inline-flex items-center gap-2 rounded-md bg-white border border-gray-200 px-3 py-1 text-sm text-gray-700 hover:bg-gray-50">Remove</button>
                        </div>

                        @error('avatar')<div class="text-red-500 text-sm mt-2">{{ $message }}</div>@enderror

                        <script>
                            function avatarUploader() {
                                return {
                                    previewUrl: null,
                                    previewName: null,
                                    isDragging: false,
                                    handleFile(event) {
                                        const file = event.target.files ? event.target.files[0]  : null;
                                        if (!file) return;
                                        if (!file.type.startsWith('image/')) {
                                            alert('Please select an image file.');
                                            this.$refs.fileInput.value = '';
                                            return;
                                        }
                                        this.previewName = file.name;
                                        this.previewUrl = URL.createObjectURL(file);
                                    },
                                    handleDrop(e) {
                                        this.isDragging = false;
                                        const dt = e.dataTransfer;
                                        const file = dt.files && dt.files[0];
                                        if (!file) return;
                                        if (!file.type.startsWith('image/')) {
                                            alert('Please drop an image file.');
                                            return;
                                        }
                                        // set the file input's files using DataTransfer
                                        const dataTransfer = new DataTransfer();
                                        dataTransfer.items.add(file);
                                        this.$refs.fileInput.files = dataTransfer.files;
                                        this.previewName = file.name;
                                        this.previewUrl = URL.createObjectURL(file);
                                    },
                                    removeFile() {
                                        this.$refs.fileInput.value = '';
                                        this.previewUrl = null;
                                        this.previewName = null;
                                    }
                                }
                            }
                        </script>
                    </div>
                </div>

                <div class="md:col-span-2">
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div>
                            <label for="name" class="mb-1.5 block text-sm font-medium text-gray-700">Name</label>
                            <input id="name" type="text" name="name" value="{{ old('name', $technician->name) }}" class="block w-full rounded-lg border border-gray-300 @error('name') field-error @enderror px-3.5 py-2.5 text-sm text-gray-900 shadow-sm transition focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
                            @error('name')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label for="email" class="mb-1.5 block text-sm font-medium text-gray-700">Email</label>
                            <input id="email" type="email" name="email" value="{{ old('email', $technician->email) }}" class="block w-full rounded-lg border border-gray-300 px-3.5 py-2.5 text-sm text-gray-900 shadow-sm transition focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20 @error('email') @enderror">
                            @error('email')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label for="phone" class="mb-1.5 block text-sm font-medium text-gray-700">Phone</label>
                            <input id="phone" type="text" name="phone" value="{{ old('phone', $technician->phone) }}" class="block w-full rounded-lg border border-gray-300 px-3.5 py-2.5 text-sm text-gray-900 shadow-sm transition focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20 @error('phone') @enderror">
                            @error('phone')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>

                        @if($supportsAvailability)
                            <div>
                                <label for="availability" class="mb-1.5 block text-sm font-medium text-gray-700">Availability</label>
                                <select id="availability" name="availability" class="block w-full rounded-lg border border-gray-300 @error('availability') field-error @enderror px-3.5 py-2.5 text-sm text-gray-900 shadow-sm transition focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
                                    @foreach($availabilityOptions as $availability)
                                        <option value="{{ $availability->value }}" @selected(old('availability', $technician->availabilityValue()) === $availability->value)>{{ $availability->label() }}</option>
                                    @endforeach
                                </select>
                                @error('availability')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                            </div>
                        @endif

                        <div class="flex items-end">
                            <button type="submit" class="ml-auto rounded-lg bg-primary-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-primary-700">Save Profile</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white">
            <div class="border-b border-gray-100 px-6 py-4">
                <h2 class="text-base font-semibold text-gray-900">Recent Assigned Tickets</h2>
            </div>
            @if($recentTickets->isEmpty())
                <div class="p-6 text-sm text-gray-500">No recent assigned tickets.</div>
            @else
                <div class="divide-y divide-gray-100">
                    @foreach($recentTickets as $ticket)
                        <a href="{{ route('technician.tickets.show', $ticket) }}" class="flex items-center justify-between gap-4 px-6 py-4 transition-colors hover:bg-gray-50">
                            <div class="min-w-0">
                                <p class="text-sm font-mono font-medium text-primary-600">{{ $ticket->ticket_number }}</p>
                                <p class="mt-1 text-sm font-medium text-gray-900">{{ $ticket->title }}</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <x-priority-badge :priority="$ticket->priority" />
                                <x-status-badge :status="$ticket->status" />
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
@endsection
