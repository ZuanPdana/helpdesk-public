@php
    $layoutSubtitle = __('dashboard.admin_panel');
    $layoutNavigationHeading = __('dashboard.administration');
    $layoutHomeRoute = route('admin.dashboard');
    $layoutHomeLabel = __('dashboard.title');
    $layoutLogoutLabel = __('common.sign_out');
    $layoutShowTopbarSearch = true;
    $layoutTopbarSearchAction = route('admin.tickets.index');
    $layoutTopbarSearchValue = request()->routeIs('admin.tickets.index') ? request('search', '') : '';
    $layoutTopbarSearchPlaceholder = __('dashboard.search_tickets');
    $layoutShowNotifications = true;
    $layoutShowRoleBadge = true;
    $layoutProfileRoute = route('admin.profile.show');
    $layoutNotificationReadRouteName = 'admin.notifications.read';
    $layoutNotificationViewAllRoute = route('admin.notifications.index');
    $layoutNotificationViewAllLabel = __('common.view_all');
    $layoutNavigation = [
        ['label' => __('dashboard.title'), 'href' => route('admin.dashboard'), 'active' => ['admin.dashboard'], 'icon' => 'dashboard'],
        ['label' => __('nav.ticket_management'), 'href' => route('admin.tickets.index'), 'active' => ['admin.tickets.*'], 'icon' => 'ticket'],
        ['label' => __('nav.user_management'), 'href' => route('admin.users.index'), 'active' => ['admin.users.*'], 'icon' => 'users'],
        ['label' => __('nav.technician_management'), 'href' => route('admin.technicians.index'), 'active' => ['admin.technicians.*'], 'icon' => 'wrench'],
        ['label' => __('nav.reports'), 'href' => route('admin.reports.index'), 'active' => ['admin.reports.*'], 'icon' => 'chart'],
        ['label' => __('profile.profile'), 'href' => route('admin.profile.show'), 'active' => ['admin.profile.*'], 'icon' => 'profile'],
    ];
@endphp

@include('layouts.partials.dashboard-shell')
