<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('admin.current_slider') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-100 text-slate-900">
    <main class="min-h-screen flex items-center justify-center p-4">
        @if($slider === null)
            <section class="w-full max-w-3xl bg-white border border-slate-200 rounded-2xl shadow-sm p-8 text-center">
                <h1 class="text-2xl font-semibold text-slate-900">{{ __('admin.no_current_slider') }}</h1>
            </section>
        @else
            @php($imageUrl = $slider->getFirstMediaUrl('scroll_bar_image'))
            <section class="w-full max-w-5xl overflow-hidden bg-white border border-slate-200 rounded-2xl shadow-sm">
                @if($imageUrl !== '')
                    <img src="{{ $imageUrl }}" alt="{{ $slider->getTranslation('title', app()->getLocale(), false) }}" class="h-72 w-full object-cover sm:h-96">
                @endif

                <div class="p-6 sm:p-8">
                    <h1 class="text-2xl sm:text-3xl font-semibold text-slate-900">
                        {{ $slider->getTranslation('title', app()->getLocale(), false) }}
                    </h1>
                </div>
            </section>
        @endif
    </main>
</body>
</html>
