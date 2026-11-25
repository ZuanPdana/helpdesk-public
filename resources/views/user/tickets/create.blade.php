@extends('layouts.user')

@section('title', __('tickets.create_support_ticket'))
@section('page_title', __('tickets.create_support_ticket'))
@section('breadcrumb')
    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
    <span class="text-gray-700">{{ __('tickets.create_ticket') }}</span>
@endsection

@section('content')
    <div class="max-w-3xl mx-auto">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="px-6 py-5 border-b border-gray-100">
                <h2 class="text-lg font-semibold text-gray-900">{{ __('tickets.ticket_information') }}</h2>
                <p class="mt-1 text-sm text-gray-500">{{ __('tickets.ticket_information_desc') }}</p>
            </div>

            <form method="POST" action="{{ route('user.tickets.store') }}" enctype="multipart/form-data" class="p-6 space-y-6"
                  x-data="{
                      submitting: false,
                      files: [],
                      addFiles(newFiles) {
                          for (const file of newFiles) {
                              if (this.files.length >= 5) break;
                              this.files.push({
                                  name: file.name,
                                  size: (file.size / 1024 / 1024).toFixed(2) + ' MB',
                                  type: file.type,
                                  preview: file.type.startsWith('image/') ? URL.createObjectURL(file) : null,
                              });
                          }
                      },
                      removeFile(index) {
                          if (this.files[index]?.preview) URL.revokeObjectURL(this.files[index].preview);
                          this.files.splice(index, 1);
                          const dt = new DataTransfer();
                          const input = $refs.fileInput;
                          const inputFiles = input.files;
                          for (let i = 0; i < inputFiles.length; i++) {
                              if (i !== index) dt.items.add(inputFiles[i]);
                          }
                          input.files = dt.files;
                      }
                  }"
                  @submit="submitting = true"
            >
                @csrf

                {{-- Title --}}
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-1.5">
                        {{ __('tickets.title_label') }} <span class="text-red-500">*</span>
                    </label>
                    <input
                        id="title"
                        type="text"
                        name="title"
                        value="{{ old('title') }}"
                        required
                        class="block w-full rounded-lg border border-gray-300 {{ $errors->has('title') ? 'field-error' : '' }} px-3.5 py-2.5 text-sm text-gray-900 shadow-sm placeholder:text-gray-400 focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 focus:outline-none transition"
                        placeholder="{{ __('tickets.title_brief_placeholder') }}"
                    >
                    @error('title')
                        <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Description --}}
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1.5">
                        {{ __('tickets.description_label') }} <span class="text-red-500">*</span>
                    </label>
                    <textarea
                        id="description"
                        name="description"
                        rows="4"
                        required
                        class="block w-full rounded-lg border border-gray-300 {{ $errors->has('description') ? 'field-error' : '' }} px-3.5 py-2.5 text-sm text-gray-900 shadow-sm placeholder:text-gray-400 focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 focus:outline-none transition resize-y"
                        placeholder="{{ __('tickets.description_detail_placeholder') }}"
                    >{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Location & Priority --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label for="location" class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('tickets.location_label') }}</label>
                        <input
                            id="location"
                            type="text"
                            name="location"
                            value="{{ old('location') }}"
                            class="block w-full rounded-lg border border-gray-300 {{ $errors->has('location') ? 'field-error' : '' }} px-3.5 py-2.5 text-sm text-gray-900 shadow-sm placeholder:text-gray-400 focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 focus:outline-none transition"
                            placeholder="{{ __('tickets.location_detail_placeholder') }}"
                        >
                        @error('location')
                            <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="priority" class="block text-sm font-medium text-gray-700 mb-1.5">
                            {{ __('tickets.priority_label') }} <span class="text-red-500">*</span>
                        </label>
                        <select
                            id="priority"
                            name="priority"
                            required
                            class="block w-full rounded-lg border border-gray-300 {{ $errors->has('priority') ? 'field-error' : '' }} px-3.5 py-2.5 text-sm text-gray-900 shadow-sm focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 focus:outline-none transition"
                        >
                            <option value="">{{ __('tickets.select_priority_option') }}</option>
                            @foreach($priorities as $priority)
                                <option value="{{ $priority->value }}" {{ old('priority') === $priority->value ? 'selected' : '' }}>
                                    {{ $priority->label() }}
                                </option>
                            @endforeach
                        </select>
                        @error('priority')
                            <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- File upload --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('tickets.attachments_label') }}</label>
                    <div
                        class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-primary-400 transition-colors cursor-pointer"
                        @dragover.prevent="$el.classList.add('drop-zone-active')"
                        @dragleave.prevent="$el.classList.remove('drop-zone-active')"
                        @drop.prevent="$el.classList.remove('drop-zone-active'); addFiles($event.dataTransfer.files); $refs.fileInput.files = $event.dataTransfer.files"
                        @click="$refs.fileInput.click()"
                    >
                        <svg class="w-10 h-10 mx-auto text-gray-400 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 16.5V9.75m0 0l3 3m-3-3l-3 3M6.75 19.5a4.5 4.5 0 01-1.41-8.775 5.25 5.25 0 0110.233-2.33 3 3 0 013.758 3.848A3.752 3.752 0 0118 19.5H6.75z" />
                        </svg>
                        <p class="text-sm text-gray-600"><span class="font-semibold text-primary-600">{{ __('tickets.click_to_upload') }}</span> {{ __('tickets.or_drag_drop') }}</p>
                        <p class="text-xs text-gray-400 mt-1">{{ __('tickets.attachment_formats') }}</p>
                    </div>
                    <input
                        x-ref="fileInput"
                        type="file"
                        name="attachments[]"
                        multiple
                        accept=".jpg,.jpeg,.png,.gif,.webp,.pdf,.doc,.docx,.xls,.xlsx"
                        class="hidden"
                        @change="addFiles($event.target.files)"
                    >
                    @error('attachments')
                        <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    @error('attachments.*')
                        <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                    @enderror

                    {{-- File previews --}}
                    <div x-show="files.length > 0" class="mt-3 space-y-2">
                        <template x-for="(file, index) in files" :key="index">
                            <div class="flex items-center gap-3 px-3 py-2 bg-gray-50 rounded-lg">
                                <template x-if="file.preview">
                                    <img :src="file.preview" class="w-10 h-10 rounded object-cover shrink-0" alt="Preview">
                                </template>
                                <template x-if="!file.preview">
                                    <div class="w-10 h-10 rounded bg-gray-200 flex items-center justify-center shrink-0">
                                        <svg class="w-5 h-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                                        </svg>
                                    </div>
                                </template>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm text-gray-700 truncate" x-text="file.name"></p>
                                    <p class="text-xs text-gray-400" x-text="file.size"></p>
                                </div>
                                <button type="button" @click="removeFile(index)" class="text-gray-400 hover:text-red-500 transition-colors shrink-0">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                                </button>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                    <a href="{{ route('user.tickets.index') }}" class="rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50 transition-colors">
                        {{ __('common.cancel') }}
                    </a>
                    <button
                        type="submit"
                        :disabled="submitting"
                        class="rounded-lg bg-primary-600 px-6 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-primary-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
                    >
                        <svg x-show="submitting" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                        <span x-text="submitting ? '{{ __('tickets.submitting') }}' : '{{ __('tickets.submit_ticket') }}'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
