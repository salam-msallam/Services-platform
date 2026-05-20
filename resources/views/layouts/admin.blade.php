<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', __('admin.admin_panel'))</title>
    @php
        $viteAssets = ['resources/css/app.css', 'resources/js/app.js'];
        if (filled(config('services.firebase_web.apiKey'))) {
            $viteAssets[] = 'resources/js/admin-fcm.js';
        }
    @endphp
    @vite($viteAssets)
    @if(filled(config('services.firebase_web.apiKey')))
        @php
            $firebaseWebClient = array_filter(
                config('services.firebase_web', []),
                static fn (mixed $v): bool => $v !== null && $v !== '',
            );
        @endphp
        <meta name="firebase-messaging-sw-url" content="{{ route('firebase.messaging.sw') }}">
        <meta name="admin-device-token-url" content="{{ route('admin.notifications.device-token') }}">
        <script type="application/json" id="firebase-web-config">@json($firebaseWebClient)</script>
    @endif
</head>
@php
    $locale = app()->getLocale();
@endphp
<body class="bg-slate-100 text-slate-900">
<div class="flex h-screen">
    <aside class="w-72 bg-indigo-950 text-indigo-100 flex flex-col border-r border-indigo-900/50 {{ $locale === 'ar' ? 'border-l border-r-0' : 'border-r' }}">
        <div class="h-16 px-4 flex items-center border-b border-indigo-900/50">
            <div class="flex items-center gap-3">
                <div class="h-9 w-9 rounded-xl bg-indigo-600/90 flex items-center justify-center">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 2l9 5v10l-9 5-9-5V7l9-5Z" stroke="white" stroke-width="2" stroke-linejoin="round"/>
                        <path d="M12 22V12" stroke="white" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                </div>
                <div class="leading-tight">
                    <div class="text-sm font-semibold">{{ __('admin.admin_panel') }}</div>
                    <div class="text-xs text-indigo-200/70">{{ __('admin.navigation') }}</div>
                </div>
            </div>
        </div>

        <nav class="px-3 py-4 flex-1">
            <ul class="space-y-1">
                <li>
                    <a
                        href="{{ route('admin.dashboard') }}"
                        class="group flex items-center gap-3 rounded-xl px-3 py-2 text-sm font-medium transition
                            {{ request()->routeIs('admin.dashboard') ? 'bg-indigo-600 text-white' : 'text-indigo-100 hover:bg-indigo-900/60 hover:text-white' }}"
                    >
                        <svg class="shrink-0" width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M3 12l9-9 9 9v9a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1v-9Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                        </svg>
                        {{ __('admin.dashboard') }}
                    </a>
                </li>

                @can('manage admins')
                    <li>
                        <a
                            href="{{ route('admin.admins.index') }}"
                            class="group flex items-center gap-3 rounded-xl px-3 py-2 text-sm font-medium transition
                                {{ request()->routeIs('admin.admins.index') || request()->routeIs('admin.admins.create') ? 'bg-indigo-600 text-white' : 'text-indigo-100 hover:bg-indigo-900/60 hover:text-white' }}"
                        >
                            <svg class="shrink-0" width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                <path d="M8.5 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z" stroke="currentColor" stroke-width="2"/>
                                <path d="M20 8v6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                <path d="M23 11h-6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            </svg>
                            {{ __('admin.manage_admins') }}
                        </a>
                    </li>
                @endcan

                @can('view users')
                    <li>
                        <a
                            href="{{ route('admin.app-users.index') }}"
                            class="group flex items-center gap-3 rounded-xl px-3 py-2 text-sm font-medium transition
                                {{ request()->routeIs('admin.app-users.*') ? 'bg-indigo-600 text-white' : 'text-indigo-100 hover:bg-indigo-900/60 hover:text-white' }}"
                        >
                            <svg class="shrink-0" width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                <path d="M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z" stroke="currentColor" stroke-width="2"/>
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75" stroke="currentColor" stroke-width="2"/>
                            </svg>
                            {{ __('admin.manage_users') }}
                        </a>
                    </li>
                @endcan

                @can('manage roles')
                    <li>
                        <a
                            href="{{ route('admin.roles.index') }}"
                            class="group flex items-center gap-3 rounded-xl px-3 py-2 text-sm font-medium transition
                                {{ request()->routeIs('admin.roles.*') || request()->routeIs('admin.roles.permissions.*') ? 'bg-indigo-600 text-white' : 'text-indigo-100 hover:bg-indigo-900/60 hover:text-white' }}"
                        >
                            <svg class="shrink-0" width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12 2l3 4 5 1-3 4 .5 5L12 14l-5.5 2 .5-5-3-4 5-1 3-4Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                            </svg>
                            {{ __('admin.manage_roles') }}
                        </a>
                    </li>
                @endcan

                @can('manage activity types')
                    <li>
                        <a
                            href="{{ route('admin.activity-types.index') }}"
                            class="group flex items-center gap-3 rounded-xl px-3 py-2 text-sm font-medium transition
                                {{ request()->routeIs('admin.activity-types.*') ? 'bg-indigo-600 text-white' : 'text-indigo-100 hover:bg-indigo-900/60 hover:text-white' }}"
                        >
                            <svg class="shrink-0" width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M4 7h16M4 12h16M4 17h16" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            </svg>
                            {{ __('admin.manage_activity_types') }}
                        </a>
                    </li>
                @endcan

                @hasanyrole('super-admin|business-auditor')
                    <li>
                        <a
                            href="{{ route('admin.business-accounts.index') }}"
                            class="group flex items-center gap-3 rounded-xl px-3 py-2 text-sm font-medium transition
                                {{ request()->routeIs('admin.business-accounts.*') ? 'bg-indigo-600 text-white' : 'text-indigo-100 hover:bg-indigo-900/60 hover:text-white' }}"
                        >
                            <svg class="shrink-0" width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12 2l9 5v10l-9 5-9-5V7l9-5Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                            </svg>
                            {{ __('admin.business_accounts_review_menu') }}
                        </a>
                    </li>
                @endhasanyrole

                @hasanyrole('super-admin|business-auditor')
                    <li>
                        <a
                            href="{{ route('admin.reports.index') }}"
                            class="group flex items-center gap-3 rounded-xl px-3 py-2 text-sm font-medium transition
                                {{ request()->routeIs('admin.reports.*') ? 'bg-indigo-600 text-white' : 'text-indigo-100 hover:bg-indigo-900/60 hover:text-white' }}"
                        >
                            <svg class="shrink-0" width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12 3l8 4.5v9L12 21l-8-4.5v-9L12 3Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                                <path d="M9 10h6M9 14h4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            </svg>
                            {{ __('api.report_management_menu') }}
                        </a>
                    </li>
                @endhasanyrole

                @hasanyrole('super-admin|service-moderator')
                    <li>
                        <a
                            href="{{ route('admin.services.index') }}"
                            class="group flex items-center gap-3 rounded-xl px-3 py-2 text-sm font-medium transition
                                {{ request()->routeIs('admin.services.*') ? 'bg-indigo-600 text-white' : 'text-indigo-100 hover:bg-indigo-900/60 hover:text-white' }}"
                        >
                            <svg class="shrink-0" width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M4 5h16v14H4V5Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                                <path d="M8 9h8M8 13h8" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            </svg>
                            {{ __('admin.services_review_menu') }}
                        </a>
                    </li>
                @endhasanyrole

                @can('manage cities')
                    <li>
                        <a
                            href="{{ route('admin.cities.index') }}"
                            class="group flex items-center gap-3 rounded-xl px-3 py-2 text-sm font-medium transition
                                {{ request()->routeIs('admin.cities.*') ? 'bg-indigo-600 text-white' : 'text-indigo-100 hover:bg-indigo-900/60 hover:text-white' }}"
                        >
                            <svg class="shrink-0" width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12 21s7-6.3 7-11a7 7 0 1 0-14 0c0 4.7 7 11 7 11Z" stroke="currentColor" stroke-width="2"/>
                                <circle cx="12" cy="10" r="2.5" stroke="currentColor" stroke-width="2"/>
                            </svg>
                            {{ __('admin.manage_cities') }}
                        </a>
                    </li>
                @endcan

                @can('manage categories')
                    <li>
                        <a
                            href="{{ route('admin.categories.index') }}"
                            class="group flex items-center gap-3 rounded-xl px-3 py-2 text-sm font-medium transition
                                {{ request()->routeIs('admin.categories.*') ? 'bg-indigo-600 text-white' : 'text-indigo-100 hover:bg-indigo-900/60 hover:text-white' }}"
                        >
                            <svg class="shrink-0" width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M4 5h16v14H4V5Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                                <path d="M8 9h8M8 13h8" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            </svg>
                            {{ __('admin.manage_categories') }}
                        </a>
                    </li>
                @endcan

                @can('manage sub-categories')
                    <li>
                        <a
                            href="{{ route('admin.sub-categories.index') }}"
                            class="group flex items-center gap-3 rounded-xl px-3 py-2 text-sm font-medium transition
                                {{ request()->routeIs('admin.sub-categories.*') ? 'bg-indigo-600 text-white' : 'text-indigo-100 hover:bg-indigo-900/60 hover:text-white' }}"
                        >
                            <svg class="shrink-0" width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M4 6h16M4 12h10M4 18h16" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            </svg>
                            {{ __('admin.manage_sub_categories') }}
                        </a>
                    </li>
                @endcan

                @can('view-sliders')
                    <li>
                        <a
                            href="{{ route('admin.sliders.index') }}"
                            class="group flex items-center gap-3 rounded-xl px-3 py-2 text-sm font-medium transition
                                {{ request()->routeIs('admin.sliders.*') ? 'bg-indigo-600 text-white' : 'text-indigo-100 hover:bg-indigo-900/60 hover:text-white' }}"
                        >
                            <svg class="shrink-0" width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M4 6h16v12H4V6Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                                <path d="M8 10h4M8 14h8" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            </svg>
                            {{ __('admin.manage_sliders') }}
                        </a>
                    </li>
                @endcan

            </ul>
        </nav>

        <div class="px-4 pb-5 text-xs text-indigo-200/70">
            {{ __('admin.all_rights') }}
        </div>
    </aside>

    <div class="flex-1 flex flex-col overflow-hidden">
        <header class="h-16 bg-white border-b border-slate-200 px-4 flex items-center gap-3">
            <div class="flex-1 min-w-0">
                <div class="text-sm font-semibold text-slate-800 truncate">
                    @yield('title', __('admin.admin_panel'))
                </div>
            </div>

            <div class="flex items-center gap-2">
                <div class="hidden sm:flex items-center gap-2">
                    <a
                        href="{{ route('admin.locale', 'ar') }}"
                        class="px-3 py-2 rounded-lg text-sm font-medium transition
                            {{ $locale === 'ar' ? 'bg-indigo-600 text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}"
                    >
                        عربي
                    </a>
                    <a
                        href="{{ route('admin.locale', 'en') }}"
                        class="px-3 py-2 rounded-lg text-sm font-medium transition
                            {{ $locale === 'en' ? 'bg-indigo-600 text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}"
                    >
                        EN
                    </a>
                </div>

                <div class="flex items-center gap-3">
                    @auth
                        @php
                            /** @var \App\Models\User $authUser */
                            $authUser = auth()->user();
                            $unreadCount = $authUser->unreadNotifications()->count();
                            $latestNotifications = $authUser->notifications()->latest()->limit(10)->get();
                        @endphp

                        <div id="notificationDropdown" class="relative">
                            <button
                                id="notificationToggle"
                                type="button"
                                class="relative inline-flex items-center justify-center h-10 w-10 rounded-xl border border-slate-300 bg-white hover:bg-slate-50"
                                aria-label="Notifications"
                                aria-expanded="false"
                            >
                                <svg class="h-5 w-5 text-slate-700" viewBox="0 0 24 24" fill="none">
                                    <path d="M15 17h5l-1.4-1.4a2 2 0 0 1-.6-1.4V11a6 6 0 1 0-12 0v3.2c0 .5-.2 1-.6 1.4L4 17h5m6 0a3 3 0 1 1-6 0m6 0H9" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>

                                @if($unreadCount > 0)
                                    <span class="absolute -top-1 -right-1 min-w-[1.25rem] h-5 px-1 rounded-full bg-rose-600 text-white text-[11px] flex items-center justify-center">
                                        {{ $unreadCount > 99 ? '99+' : $unreadCount }}
                                    </span>
                                @endif
                            </button>

                            <div
                                id="notificationPanel"
                                class="absolute {{ app()->getLocale() === 'ar' ? 'left-0' : 'right-0' }} mt-2 w-96 max-w-[90vw] bg-white border border-slate-200 rounded-2xl shadow-lg z-50"
                                style="display: none;"
                            >
                                <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between">
                                    <p class="text-sm font-semibold text-slate-800">Notifications</p>

                                    @if($unreadCount > 0)
                                        <button
                                            type="button"
                                            class="text-xs font-medium text-indigo-600 hover:text-indigo-700"
                                            onclick="markAllAsRead('{{ route('admin.notifications.mark-all-as-read') }}')"
                                        >
                                            Mark all as read
                                        </button>
                                    @endif
                                </div>

                                <div class="max-h-96 overflow-y-auto divide-y divide-slate-100">
                                    @forelse($latestNotifications as $notification)
                                        @php
                                            $data = $notification->data ?? [];
                                            $url = $data['url'] ?? '#';
                                            $type = $data['type'] ?? 'general';
                                            $isUnread = $notification->read_at === null;
                                        @endphp

                                        <a
                                            href="{{ $url }}"
                                            class="block px-4 py-3 hover:bg-slate-50 {{ $isUnread ? 'bg-indigo-50/40' : '' }}"
                                            onclick="event.preventDefault(); markAsReadThenGo('{{ route('admin.notifications.mark-as-read', ['notificationId' => $notification->id]) }}','{{ $url }}');"
                                        >
                                            <div class="flex items-start gap-3">
                                                <span class="mt-1 inline-flex rounded-full px-2 py-0.5 text-[11px] font-semibold bg-slate-100 text-slate-700">
                                                    {{ $type }}
                                                </span>
                                                <div class="min-w-0">
                                                    <p class="text-sm font-semibold text-slate-900">{{ $data['title'] ?? 'Notification' }}</p>
                                                    <p class="text-xs text-slate-600 mt-1">{{ $data['message'] ?? '' }}</p>
                                                    <p class="text-[11px] text-slate-400 mt-1">{{ $notification->created_at?->diffForHumans() }}</p>
                                                </div>
                                            </div>
                                        </a>
                                    @empty
                                        <div class="px-4 py-8 text-center text-sm text-slate-500">
                                            No notifications yet.
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>

                        <a
                            href="{{ route('admin.profile.edit') }}"
                            class="block rounded-xl px-3 py-2 text-right transition hover:bg-slate-100 {{ $locale === 'ar' ? 'text-right' : 'text-left' }}"
                        >
                            <div class="text-sm font-semibold text-slate-900">
                                {{ auth()->user()->name }}
                            </div>
                            @if(auth()->user()->admin)
                                <div class="text-xs text-slate-500">
                                    {{ auth()->user()->admin->email }}
                                </div>
                            @endif
                        </a>
                        <a
                            href="{{ route('admin.profile.edit') }}"
                            class="inline-flex items-center px-3 py-2 rounded-xl border border-slate-300 text-sm font-medium text-slate-700 hover:bg-slate-50 transition"
                        >
                            {{ __('admin.profile') }}
                        </a>
                        <form method="POST" action="{{ route('admin.logout') }}">
                            @csrf
                            <button
                                type="submit"
                                class="inline-flex items-center px-3 py-2 rounded-xl border border-slate-300 text-sm font-medium text-slate-700 hover:bg-slate-50 transition"
                            >
                                {{ __('admin.logout') }}
                            </button>
                        </form>
                    @endauth
                </div>
            </div>
        </header>

        <main class="flex-1 overflow-y-auto p-6">
            @yield('content')
            @yield('scripts')
        </main>
    </div>
</div>
<script>
    (function initNotificationDropdown() {
        const dropdown = document.getElementById('notificationDropdown');
        const toggle = document.getElementById('notificationToggle');
        const panel = document.getElementById('notificationPanel');

        if (!dropdown || !toggle || !panel) {
            return;
        }

        const isOpen = () => panel.style.display !== 'none';

        const openPanel = () => {
            panel.style.display = 'block';
            toggle.setAttribute('aria-expanded', 'true');
        };

        const closePanel = () => {
            panel.style.display = 'none';
            toggle.setAttribute('aria-expanded', 'false');
        };

        toggle.addEventListener('click', (event) => {
            event.stopPropagation();
            if (isOpen()) {
                closePanel();
                return;
            }

            openPanel();
        });

        document.addEventListener('click', (event) => {
            if (!dropdown.contains(event.target)) {
                closePanel();
            }
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                closePanel();
            }
        });
    })();

    async function markAsReadThenGo(markUrl, targetUrl) {
        try {
            await fetch(markUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                credentials: 'same-origin'
            });
        } catch (_) {
            // Do not block navigation if mark-as-read fails.
        }

        window.location.href = targetUrl;
    }

    async function markAllAsRead(markAllUrl) {
        try {
            await fetch(markAllUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                credentials: 'same-origin'
            });
        } catch (_) {
            // Fallback to refresh even if request fails.
        }

        window.location.reload();
    }
</script>
</body>
</html>
