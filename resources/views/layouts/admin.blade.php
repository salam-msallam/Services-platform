<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', __('admin.admin_panel'))</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
@php
    $locale = app()->getLocale();
@endphp
<body class="bg-slate-100 text-slate-900">
<div class="flex h-screen {{ $locale === 'ar' ? 'flex-row-reverse' : '' }}">
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
                        <div class="text-right {{ $locale === 'ar' ? 'text-right' : 'text-left' }}">
                            <div class="text-sm font-semibold text-slate-900">
                                {{ auth()->user()->name }}
                            </div>
                            @if(auth()->user()->admin)
                                <div class="text-xs text-slate-500">
                                    {{ auth()->user()->admin->email }}
                                </div>
                            @endif
                        </div>
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
</body>
</html>

