@php
    $managedUser ??= null;
@endphp

<div class="space-y-6">
    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
        <div>
            <label for="name" class="mb-1.5 block text-sm font-medium text-gray-700">Name <span class="text-red-500">*</span></label>
            <input id="name" type="text" name="name" value="{{ old('name', $managedUser?->name) }}" required class="block w-full rounded-lg border border-gray-300 @error('name') field-error @enderror px-3.5 py-2.5 text-sm text-gray-900 shadow-sm transition focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
            @error('name')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="email" class="mb-1.5 block text-sm font-medium text-gray-700">Email <span class="text-red-500">*</span></label>
            <input id="email" type="email" name="email" value="{{ old('email', $managedUser?->email) }}" required class="block w-full rounded-lg border border-gray-300 @error('email') field-error @enderror px-3.5 py-2.5 text-sm text-gray-900 shadow-sm transition focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
            @error('email')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="phone" class="mb-1.5 block text-sm font-medium text-gray-700">Phone</label>
            <input id="phone" type="text" name="phone" value="{{ old('phone', $managedUser?->phone) }}" class="block w-full rounded-lg border border-gray-300 @error('phone') field-error @enderror px-3.5 py-2.5 text-sm text-gray-900 shadow-sm transition focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
            @error('phone')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="department" class="mb-1.5 block text-sm font-medium text-gray-700">Department</label>
            <input id="department" type="text" name="department" value="{{ old('department', $managedUser?->department) }}" class="block w-full rounded-lg border border-gray-300 @error('department') field-error @enderror px-3.5 py-2.5 text-sm text-gray-900 shadow-sm transition focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
            @error('department')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="role" class="mb-1.5 block text-sm font-medium text-gray-700">Role <span class="text-red-500">*</span></label>
            <select id="role" name="role" required class="block w-full rounded-lg border border-gray-300 @error('role') field-error @enderror px-3.5 py-2.5 text-sm text-gray-900 shadow-sm transition focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
                @foreach($roles as $role)
                    <option value="{{ $role->value }}" @selected(old('role', $managedUser?->role?->value) === $role->value)>{{ $role->label() }}</option>
                @endforeach
            </select>
            @error('role')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="status" class="mb-1.5 block text-sm font-medium text-gray-700">Status</label>
            <select id="status" name="status" @disabled(! $supportsStatus) class="block w-full rounded-lg border border-gray-300 @error('status') field-error @enderror px-3.5 py-2.5 text-sm text-gray-900 shadow-sm transition focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20 disabled:cursor-not-allowed disabled:bg-gray-100">
                @foreach($statuses as $status)
                    <option value="{{ $status->value }}" @selected(old('status', $managedUser?->accountStatusValue()) === $status->value)>{{ $status->label() }}</option>
                @endforeach
            </select>
            @if(! $supportsStatus)
                <p class="mt-1.5 text-xs text-amber-600">This field becomes editable after the pending user status migration is applied.</p>
            @endif
            @error('status')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
        <div>
            <label for="password" class="mb-1.5 block text-sm font-medium text-gray-700">
                Password @if(! $managedUser)<span class="text-red-500">*</span>@endif
            </label>
            <input id="password" type="password" name="password" {{ $managedUser ? '' : 'required' }} class="block w-full rounded-lg border border-gray-300 @error('password') field-error @enderror px-3.5 py-2.5 text-sm text-gray-900 shadow-sm transition focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
            <p class="mt-1.5 text-xs text-gray-500">{{ $managedUser ? 'Leave blank to keep the existing password.' : 'Use at least 8 characters.' }}</p>
            @error('password')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="password_confirmation" class="mb-1.5 block text-sm font-medium text-gray-700">Confirm Password @if(! $managedUser)<span class="text-red-500">*</span>@endif</label>
            <input id="password_confirmation" type="password" name="password_confirmation" {{ $managedUser ? '' : 'required' }} class="block w-full rounded-lg border border-gray-300 px-3.5 py-2.5 text-sm text-gray-900 shadow-sm transition focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
        </div>
    </div>
</div>
