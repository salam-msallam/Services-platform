@extends('layouts.admin')

@section('title', __('admin.service_details'))

@section('content')
    @php
        $locale = app()->getLocale();
        $statusValue = $service->status?->value ?? (string) $service->status;
        $images = $service->getMedia('images');
        $dynamicValues = is_array($service->dynamic_values) ? $service->dynamic_values : [];
    @endphp

    <div class="space-y-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
            <h1 class="text-2xl font-semibold text-slate-900">{{ __('admin.service_details') }}</h1>
            <a href="{{ route('admin.services.index') }}" class="inline-flex items-center px-4 py-2.5 rounded-xl border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 text-sm font-semibold">
                {{ __('admin.back_to_services') }}
            </a>
        </div>

        @if(session('success'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl px-4 py-3 text-sm">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="bg-rose-50 border border-rose-200 text-rose-700 rounded-2xl px-4 py-3 text-sm">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6 space-y-5">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <div class="text-xs text-slate-500 uppercase">{{ __('admin.service_title') }}</div>
                    <div class="mt-1 text-slate-900 font-medium">
                        {{ $service->getTranslation('title', $locale) ?? '-' }}
                    </div>
                </div>
                <div>
                    <div class="text-xs text-slate-500 uppercase">{{ __('admin.status') }}</div>
                    <div class="mt-1 text-slate-900 font-medium">{{ $statusValue }}</div>
                </div>

                <div>
                    <div class="text-xs text-slate-500 uppercase">{{ __('admin.business_account') }}</div>
                    <div class="mt-1 text-slate-900 font-medium">
                        {{ $service->businessAccount?->getTranslation('name', $locale) ?? '-' }}
                    </div>
                </div>
                <div>
                    <div class="text-xs text-slate-500 uppercase">{{ __('admin.category') }}</div>
                    <div class="mt-1 text-slate-900 font-medium">
                        {{ $service->category?->getTranslation('name', $locale) ?? '-' }}
                        @if($service->subCategory)
                            <span class="text-slate-400">/</span>
                            {{ $service->subCategory->getTranslation('name', $locale) }}
                        @endif
                    </div>
                </div>

                <div>
                    <div class="text-xs text-slate-500 uppercase">{{ __('admin.city') }}</div>
                    <div class="mt-1 text-slate-900 font-medium">
                        {{ $service->city?->getTranslation('name', $locale) ?? '-' }}
                    </div>
                </div>
                <div>
                    <div class="text-xs text-slate-500 uppercase">{{ __('admin.created_at') }}</div>
                    <div class="mt-1 text-slate-900 font-medium">{{ $service->created_at }}</div>
                </div>

                <div>
                    <div class="text-xs text-slate-500 uppercase">{{ __('admin.work_type') }}</div>
                    <div class="mt-1 text-slate-900 font-medium">{{ $service->work_type }}</div>
                </div>
                <div>
                    <div class="text-xs text-slate-500 uppercase">{{ __('admin.quantity') }}</div>
                    <div class="mt-1 text-slate-900 font-medium">{{ $service->quantity }}</div>
                </div>

                <div>
                    <div class="text-xs text-slate-500 uppercase">{{ __('admin.price_syp') }}</div>
                    <div class="mt-1 text-slate-900 font-medium">{{ $service->price_syp ?? '-' }}</div>
                </div>
                <div>
                    <div class="text-xs text-slate-500 uppercase">{{ __('admin.price_usd') }}</div>
                    <div class="mt-1 text-slate-900 font-medium">{{ $service->price_usd ?? '-' }}</div>
                </div>
                <div>
                    <div class="text-xs text-slate-500 uppercase">{{ __('admin.property_type') }}</div>
                    <div class="mt-1 text-slate-900 font-medium">{{ $service->property_type }}</div>
                </div>
                <div>
                    <div class="text-xs text-slate-500 uppercase">{{ __('admin.latitude') }}</div>
                    <div class="mt-1 text-slate-900 font-medium">{{ $service->latitude ?? '-' }}</div>
                </div>
                <div>
                    <div class="text-xs text-slate-500 uppercase">{{ __('admin.longitude') }}</div>
                    <div class="mt-1 text-slate-900 font-medium">{{ $service->longitude ?? '-' }}</div>
                </div>
            </div>

            <div>
                <div class="text-xs text-slate-500 uppercase">{{ __('admin.description') }}</div>
                <div class="mt-1 text-slate-900">
                    @php $desc = $service->description ? $service->getTranslation('description', $locale) : null; @endphp
                    {{ $desc ?: '-' }}
                </div>
            </div>

            <div>
                <div class="text-xs text-slate-500 uppercase">{{ __('admin.dynamic_values') }}</div>
                <div class="mt-2">
                    @if($dynamicValues === [])
                        <div class="text-sm text-slate-500">-</div>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            @foreach($dynamicValues as $k => $v)
                                <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
                                    <div class="text-xs text-slate-500 uppercase">{{ $k }}</div>
                                    <div class="mt-1 text-slate-900 font-medium">
                                        @if(is_bool($v))
                                            {{ $v ? __('admin.yes') : __('admin.no') }}
                                        @elseif(is_array($v))
                                            {{ json_encode($v, JSON_UNESCAPED_UNICODE) }}
                                        @else
                                            {{ (string) $v }}
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            @if($statusValue === 'pending')
                <div class="pt-2">
                    <div class="flex flex-wrap gap-2">
                        @can('approve services')
                            <form method="POST" action="{{ route('admin.services.accept', $service) }}">
                                @csrf
                                <input type="hidden" name="back_to_show" value="1">
                                <button type="submit" class="inline-flex items-center px-3 py-1.5 rounded-lg bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-xs font-semibold">
                                    {{ __('admin.accept') }}
                                </button>
                            </form>
                        @endcan
                        @can('reject services')
                            <form method="POST" action="{{ route('admin.services.reject', $service) }}">
                                @csrf
                                <input type="hidden" name="back_to_show" value="1">
                                <button type="submit" class="inline-flex items-center px-3 py-1.5 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-semibold">
                                    {{ __('admin.reject') }}
                                </button>
                            </form>
                        @endcan
                    </div>
                </div>
            @endif
        </div>

        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6 space-y-4">
            <h2 class="text-lg font-semibold text-slate-900">{{ __('admin.images') }}</h2>
            @if($images->isEmpty())
                <p class="text-sm text-slate-500">{{ __('admin.no_images') }}</p>
            @else
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                    @foreach($images as $image)
                        @php
                            $imageUrl = url('storage/'.$image->getPathRelativeToRoot());
                        @endphp
                        <a href="{{ $imageUrl }}" target="_blank" rel="noopener noreferrer" title="{{ $image->file_name }}" class="block border border-slate-200 rounded-xl overflow-hidden">
                            <img src="{{ $imageUrl }}" alt="{{ $image->file_name }}" class="w-full h-36 object-cover">
                        </a>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6 space-y-4">
            <div class="flex items-center justify-between gap-3">
                <h2 class="text-lg font-semibold text-slate-900">{{ __('admin.location_map') }}</h2>
                @if(is_numeric($service->latitude) && is_numeric($service->longitude))
                    <span class="inline-flex items-center rounded-full bg-indigo-50 px-3 py-1 text-xs font-semibold text-indigo-700">
                        {{ number_format((float) $service->latitude, 5) }}, {{ number_format((float) $service->longitude, 5) }}
                    </span>
                @endif
            </div>
            @php
                $hasCoordinates = is_numeric($service->latitude) && is_numeric($service->longitude);
            @endphp
            @if($hasCoordinates)
                @php
                    $latitude = (float) $service->latitude;
                    $longitude = (float) $service->longitude;

                    $delta = 0.01;
                    $left = $longitude - $delta;
                    $right = $longitude + $delta;
                    $top = $latitude + $delta;
                    $bottom = $latitude - $delta;
                    $mapSrc = "https://www.openstreetmap.org/export/embed.html?bbox={$left}%2C{$bottom}%2C{$right}%2C{$top}&layer=mapnik&marker={$latitude}%2C{$longitude}";
                    $openStreetMapUrl = "https://www.openstreetmap.org/?mlat={$latitude}&mlon={$longitude}#map=15/{$latitude}/{$longitude}";
                @endphp

                <div class="relative w-full h-96 rounded-2xl border border-slate-200 overflow-hidden bg-slate-100 shadow-inner">
                    <iframe
                        src="{{ $mapSrc }}"
                        class="w-full h-full border-0"
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"
                        title="Service location map"
                    ></iframe>
                    <div class="pointer-events-none absolute inset-x-0 top-0 h-16 bg-gradient-to-b from-slate-900/10 to-transparent"></div>
                </div>

                <div class="flex items-center justify-end">
                    <a
                        href="{{ $openStreetMapUrl }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="inline-flex items-center gap-1.5 rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-50"
                    >
                        {{ __('admin.open_in_openstreetmap') }}
                    </a>
                </div>
            @else
                <p class="text-sm text-slate-500">{{ __('admin.service_coordinates_unavailable') }}</p>
            @endif
        </div>
    </div>
@endsection

