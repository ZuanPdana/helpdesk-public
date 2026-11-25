@extends('layouts.admin')

@section('title', __('common.edit'))
@section('page_title', __('common.edit'))
@section('breadcrumb')
    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
    <a href="{{ route('admin.users.index') }}" class="hover:text-gray-700">Users</a>
    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
    <span class="text-gray-700">{{ $managedUser->name }}</span>
@endsection

@section('content')
    <div class="mx-auto max-w-4xl">
        <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
            <div class="border-b border-gray-100 px-6 py-5">
                <h1 class="text-lg font-semibold text-gray-900">{{ __('common.edit') }} {{ __('admin.users') }}</h1>
                <p class="mt-1 text-sm text-gray-500">Update the account details for {{ $managedUser->name }}.</p>
            </div>

            <form method="POST" action="{{ route('admin.users.update', $managedUser) }}" class="space-y-6 p-6">
                @csrf
                @method('PUT')

                @include('admin.users._form')

                <div class="flex items-center justify-end gap-3 border-t border-gray-100 pt-4">
                    <a href="{{ route('admin.users.show', $managedUser) }}" class="rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm transition-colors hover:bg-gray-50">Cancel</a>
                    <button type="submit" class="rounded-lg bg-primary-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-primary-700">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
@endsection
