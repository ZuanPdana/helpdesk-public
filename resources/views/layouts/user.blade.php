@php
    $layoutSubtitle = __('dashboard.ticketing_system');
    $layoutNavigationHeading = __('dashboard.navigation');
    $layoutHomeRoute = route('user.dashboard');
    $layoutHomeLabel = __('dashboard.title');
    $layoutLogoutLabel = __('common.sign_out');
    $layoutShowTopbarSearch = false;
    $layoutTopbarSearchAction = route('user.tickets.index');
    $layoutTopbarSearchValue = '';
    $layoutTopbarSearchPlaceholder = __('dashboard.search_placeholder');
    $layoutShowNotifications = true;
    $layoutShowRoleBadge = false;
    $layoutProfileRoute = route('user.profile.show');
    $layoutNotificationReadRouteName = 'user.notifications.read';
    $layoutNotificationViewAllRoute = route('user.tickets.index');
    $layoutNotificationViewAllLabel = __('dashboard.my_tickets');
    $layoutNavigation = [
        ['label' => __('dashboard.title'), 'href' => route('user.dashboard'), 'active' => ['user.dashboard'], 'icon' => 'dashboard'],
        ['label' => __('tickets.create_ticket'), 'href' => route('user.tickets.create'), 'active' => ['user.tickets.create'], 'icon' => 'profile'],
        ['label' => __('tickets.my_tickets'), 'href' => route('user.tickets.index'), 'active' => ['user.tickets.index', 'user.tickets.show'], 'icon' => 'ticket'],
        ['label' => __('profile.profile'), 'href' => route('user.profile.show'), 'active' => ['user.profile.*'], 'icon' => 'profile'],
    ];
@endphp

@include('layouts.partials.dashboard-shell')
