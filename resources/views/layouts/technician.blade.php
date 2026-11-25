@php
    $layoutSubtitle = __('dashboard.technician_panel');
    $layoutNavigationHeading = __('dashboard.technician_menu');
    $layoutHomeRoute = route('technician.dashboard');
    $layoutHomeLabel = __('dashboard.title');
    $layoutLogoutLabel = __('common.sign_out');
    $layoutShowTopbarSearch = true;
    $layoutTopbarSearchAction = route('technician.tickets.index');
    $layoutTopbarSearchValue = request()->routeIs('technician.tickets.index') ? request('search', '') : '';
    $layoutTopbarSearchPlaceholder = __('dashboard.search_tickets');
    $layoutShowNotifications = true;
    $layoutShowRoleBadge = true;
    $layoutProfileRoute = route('technician.profile.show');
    $layoutNotificationReadRouteName = 'technician.notifications.read';
    $layoutNotificationViewAllRoute = route('technician.tickets.index');
    $layoutNotificationViewAllLabel = __('common.view_all');
    $layoutNavigation = [
        ['label' => __('dashboard.title'), 'href' => route('technician.dashboard'), 'active' => ['technician.dashboard'], 'icon' => 'dashboard'],
        ['label' => __('tickets.assigned_tickets'), 'href' => route('technician.tickets.index'), 'active' => ['technician.tickets.*'], 'icon' => 'ticket'],
        ['label' => __('profile.profile'), 'href' => route('technician.profile.show'), 'active' => ['technician.profile.*'], 'icon' => 'profile'],
    ];
@endphp

@include('layouts.partials.dashboard-shell')
