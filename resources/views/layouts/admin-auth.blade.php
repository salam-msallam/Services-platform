<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', __('admin.admin_panel'))</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-100 text-slate-900 flex items-center justify-center p-4">
<div class="fixed inset-0 -z-10 bg-gradient-to-br from-indigo-600/15 via-indigo-500/10 to-slate-200"></div>

<main class="w-full max-w-md">
    @yield('content')
    @yield('scripts')
</main>
</body>
</html>

