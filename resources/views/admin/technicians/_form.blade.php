@php
    $technician ??= null;
    $skillsValue = old('skills_input', $technician ? implode(', ', $technician->skillsList()) : '');
@endphp

<div class="space-y-6">
    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
        <div>
            <label for="name" class="mb-1.5 block text-sm font-medium text-gray-700">Name <span class="text-red-500">*</span></label>
            <input id="name" type="text" name="name" value="{{ old('name', $technician?->name) }}" required class="block w-full rounded-lg border border-gray-300 @error('name') field-error @enderror px-3.5 py-2.5 text-sm text-gray-900 shadow-sm transition focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
            @error('name')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="email" class="mb-1.5 block text-sm font-medium text-gray-700">Email <span class="text-red-500">*</span></label>
            <input id="email" type="email" name="email" value="{{ old('email', $technician?->email) }}" required class="block w-full rounded-lg border border-gray-300 @error('email') field-error @enderror px-3.5 py-2.5 text-sm text-gray-900 shadow-sm transition focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
            @error('email')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="phone" class="mb-1.5 block text-sm font-medium text-gray-700">Phone</label>
            <input id="phone" type="text" name="phone" value="{{ old('phone', $technician?->phone) }}" class="block w-full rounded-lg border border-gray-300 @error('phone') field-error @enderror px-3.5 py-2.5 text-sm text-gray-900 shadow-sm transition focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
            @error('phone')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="department" class="mb-1.5 block text-sm font-medium text-gray-700">Department</label>
            <input id="department" type="text" name="department" value="{{ old('department', $technician?->department) }}" class="block w-full rounded-lg border border-gray-300 @error('department') field-error @enderror px-3.5 py-2.5 text-sm text-gray-900 shadow-sm transition focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
            @error('department')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="availability" class="mb-1.5 block text-sm font-medium text-gray-700">Availability</label>
            <select id="availability" name="availability" @disabled(! $supportsAvailability) class="block w-full rounded-lg border border-gray-300 @error('availability') field-error @enderror px-3.5 py-2.5 text-sm text-gray-900 shadow-sm transition focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20 disabled:cursor-not-allowed disabled:bg-gray-100">
                <option value="">Select availability</option>
                @foreach($availabilityOptions as $availability)
                    <option value="{{ $availability->value }}" @selected(old('availability', $technician?->availabilityValue()) === $availability->value)>{{ $availability->label() }}</option>
                @endforeach
            </select>
            @if(! $supportsAvailability)
                <p class="mt-1.5 text-xs text-amber-600">Availability becomes editable after the pending migration is applied.</p>
            @endif
            @error('availability')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="skills_input" class="mb-1.5 block text-sm font-medium text-gray-700">Skills</label>
            <textarea id="skills_input" name="skills_input" rows="4" @disabled(! $supportsSkills) class="block w-full rounded-lg border border-gray-300 @error('skills') field-error @enderror @error('skills.*') field-error @enderror px-3.5 py-2.5 text-sm text-gray-900 shadow-sm transition focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20 disabled:cursor-not-allowed disabled:bg-gray-100" placeholder="Hardware, Networking, Printers, Windows">{{ $skillsValue }}</textarea>
            @if(! $supportsSkills)
                <p class="mt-1.5 text-xs text-amber-600">Skills become editable after the pending migration is applied.</p>
            @else
                <p class="mt-1.5 text-xs text-gray-500">Separate skills with commas or line breaks.</p>
            @endif
            @error('skills')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
            @error('skills.*')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
        <div>
            <label for="password" class="mb-1.5 block text-sm font-medium text-gray-700">Password @if(! $technician)<span class="text-red-500">*</span>@endif</label>
            <input id="password" type="password" name="password" {{ $technician ? '' : 'required' }} class="block w-full rounded-lg border border-gray-300 @error('password') field-error @enderror px-3.5 py-2.5 text-sm text-gray-900 shadow-sm transition focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
            <p class="mt-1.5 text-xs text-gray-500">{{ $technician ? 'Leave blank to keep the existing password.' : 'Use at least 8 characters.' }}</p>
            @error('password')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="password_confirmation" class="mb-1.5 block text-sm font-medium text-gray-700">Confirm Password @if(! $technician)<span class="text-red-500">*</span>@endif</label>
            <input id="password_confirmation" type="password" name="password_confirmation" {{ $technician ? '' : 'required' }} class="block w-full rounded-lg border border-gray-300 px-3.5 py-2.5 text-sm text-gray-900 shadow-sm transition focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500/20">
        </div>
    </div>
</div>
