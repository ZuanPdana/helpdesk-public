@extends('layouts.admin')

@section('title', __('dashboard.reports'))
@section('page_title', __('dashboard.reports'))
@section('breadcrumb')
    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
    <span class="text-gray-700">{{ __('dashboard.reports') }}</span>
@endsection

@section('content')
    @php
        $maxPriority = max(1, $priorityDistribution->max('total'));
        $maxStatus = max(1, $statusDistribution->max('total'));
        $maxDepartment = max(1, $departmentStatistics->max('total'));
    @endphp

    <div class="space-y-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ __('admin.operational_reports') }}</h1>
            <p class="mt-1 text-sm text-gray-500">{{ __('admin.operational_reports_desc') }}</p>
        </div>

        <div class="grid grid-cols-1 gap-6 xl:grid-cols-2">
            <div class="rounded-xl border border-gray-200 bg-white p-6">
                <h2 class="text-base font-semibold text-gray-900">{{ __('admin.monthly_tickets') }}</h2>
                <div class="mt-5 space-y-4">
                    @forelse($monthlyTickets as $month)
                        <div class="flex items-center justify-between rounded-lg bg-gray-50 px-4 py-3">
                            <span class="text-sm font-medium text-gray-700">{{ $month['period'] }}</span>
                            <span class="text-sm font-semibold text-gray-900">{{ $month['total'] }}</span>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500">{{ __('admin.no_monthly_ticket_data') }}</p>
                    @endforelse
                </div>
            </div>

            <div class="rounded-xl border border-gray-200 bg-white p-6">
                <h2 class="text-base font-semibold text-gray-900">{{ __('admin.priority_distribution') }}</h2>
                <div class="mt-5 space-y-4">
                    @forelse($priorityDistribution as $row)
                        @php
                            $priorityLabel = $row->priority instanceof \App\Enums\TicketPriority
                                ? $row->priority->label()
                                : ucfirst((string) $row->priority);
                        @endphp
                        <div>
                            <div class="mb-1 flex items-center justify-between text-sm">
                                <span class="font-medium text-gray-700">{{ $priorityLabel }}</span>
                                <span class="text-gray-500">{{ $row->total }}</span>
                            </div>
                            <div class="h-2 rounded-full bg-gray-100">
                                <div class="h-2 rounded-full bg-primary-500" style="width: {{ ($row->total / $maxPriority) * 100 }}%"></div>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500">{{ __('admin.no_priority_distribution') }}</p>
                    @endforelse
                </div>
            </div>

            <div class="rounded-xl border border-gray-200 bg-white p-6">
                <h2 class="text-base font-semibold text-gray-900">{{ __('admin.status_distribution') }}</h2>
                <div class="mt-5 space-y-4">
                    @forelse($statusDistribution as $row)
                        @php
                            $statusLabel = $row->status instanceof \App\Enums\TicketStatus
                                ? $row->status->label()
                                : ucfirst(str_replace('_', ' ', (string) $row->status));
                        @endphp
                        <div>
                            <div class="mb-1 flex items-center justify-between text-sm">
                                <span class="font-medium text-gray-700">{{ $statusLabel }}</span>
                                <span class="text-gray-500">{{ $row->total }}</span>
                            </div>
                            <div class="h-2 rounded-full bg-gray-100">
                                <div class="h-2 rounded-full bg-emerald-500" style="width: {{ ($row->total / $maxStatus) * 100 }}%"></div>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500">{{ __('admin.no_status_data') }}</p>
                    @endforelse
                </div>
            </div>

            <div class="rounded-xl border border-gray-200 bg-white p-6">
                <h2 class="text-base font-semibold text-gray-900">{{ __('admin.department_statistics') }}</h2>
                <div class="mt-5 space-y-4">
                    @forelse($departmentStatistics as $row)
                        <div>
                            <div class="mb-1 flex items-center justify-between text-sm">
                                <span class="font-medium text-gray-700">{{ $row->department }}</span>
                                <span class="text-gray-500">{{ $row->total }}</span>
                            </div>
                            <div class="h-2 rounded-full bg-gray-100">
                                <div class="h-2 rounded-full bg-amber-500" style="width: {{ ($row->total / $maxDepartment) * 100 }}%"></div>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500">{{ __('admin.no_department_statistics') }}</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white">
            <div class="border-b border-gray-100 px-6 py-4">
                <h2 class="text-base font-semibold text-gray-900">{{ __('admin.technician_performance') }}</h2>
                <p class="mt-1 text-sm text-gray-500">{{ __('admin.operational_reports_desc') }}</p>
            </div>

            @if($technicianPerformance->isEmpty())
                <x-empty-state title="{{ __('admin.no_technicians_found') }}" description="{{ __('admin.no_technicians_found_desc') }}" />
            @else
                <div class="hidden overflow-x-auto md:block">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-gray-100">
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Technician</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Department</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Assigned</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Resolved</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Closed</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Active Now</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($technicianPerformance as $technician)
                                <tr class="transition-colors hover:bg-gray-50/50">
                                    <td class="px-6 py-4 text-sm font-semibold text-gray-900">{{ $technician->name }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-600">{{ $technician->department ?: __('common.none') }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-600">{{ $technician->total_assigned_tickets }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-600">{{ $technician->resolved_tickets }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-600">{{ $technician->closed_tickets }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-600">{{ $technician->active_assignments_count }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="divide-y divide-gray-100 md:hidden">
                    @foreach($technicianPerformance as $technician)
                        <div class="space-y-2 px-4 py-4">
                            <p class="text-sm font-semibold text-gray-900">{{ $technician->name }}</p>
                            <p class="text-xs text-gray-500">{{ $technician->department ?: __('common.none') }}</p>
                            <div class="grid grid-cols-2 gap-3 text-xs text-gray-600">
                                <span>Assigned: {{ $technician->total_assigned_tickets }}</span>
                                <span>Resolved: {{ $technician->resolved_tickets }}</span>
                                <span>Closed: {{ $technician->closed_tickets }}</span>
                                <span>Active: {{ $technician->active_assignments_count }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
@endsection
