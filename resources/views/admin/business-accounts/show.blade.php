@extends('layouts.admin')

@section('title', __('admin.business_account_details'))

@section('content')
    <div class="space-y-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
            <h1 class="text-2xl font-semibold text-slate-900">{{ __('admin.business_account_details') }}</h1>
            <a href="{{ route('admin.business-accounts.index') }}" class="inline-flex items-center px-4 py-2.5 rounded-xl border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 text-sm font-semibold">
                {{ __('admin.back_to_business_accounts') }}
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
                    <div class="text-xs text-slate-500 uppercase">{{ __('admin.name') }}</div>
                    <div class="mt-1 text-slate-900 font-medium">{{ $businessAccount->getTranslation('name', app()->getLocale()) }}</div>
                </div>
                <div>
                    <div class="text-xs text-slate-500 uppercase">{{ __('admin.status') }}</div>
                    <div class="mt-1 text-slate-900 font-medium">{{ $businessAccount->status->value ?? $businessAccount->status }}</div>
                </div>
                <div>
                    <div class="text-xs text-slate-500 uppercase">{{ __('admin.activity_type') }}</div>
                    <div class="mt-1 text-slate-900 font-medium">
                        @if($businessAccount->activityType !== null)
                            {{ $businessAccount->activityType->getTranslation('name', app()->getLocale()) }}
                        @endif
                    </div>
                </div>
                <div>
                    <div class="text-xs text-slate-500 uppercase">{{ __('admin.city') }}</div>
                    <div class="mt-1 text-slate-900 font-medium">
                        @if($businessAccount->city !== null)
                            {{ $businessAccount->city->getTranslation('name', app()->getLocale()) }}
                        @endif
                    </div>
                </div>
                <div>
                    <div class="text-xs text-slate-500 uppercase">{{ __('admin.license_number') }}</div>
                    <div class="mt-1 text-slate-900 font-medium">{{ $businessAccount->license_number }}</div>
                </div>
                <div>
                    <div class="text-xs text-slate-500 uppercase">{{ __('admin.created_at') }}</div>
                    <div class="mt-1 text-slate-900 font-medium">{{ $businessAccount->created_at }}</div>
                </div>
            </div>

            <div>
                <div class="text-xs text-slate-500 uppercase">{{ __('admin.description') }}</div>
                <div class="mt-1 text-slate-900">
                    {{ $businessAccount->description ? $businessAccount->getTranslation('description', app()->getLocale()) : '-' }}
                </div>
            </div>

            <div>
                <div class="text-xs text-slate-500 uppercase">{{ __('admin.activities') }}</div>
                <div class="mt-1 text-slate-900">
                    {{ $businessAccount->activities ? $businessAccount->getTranslation('activities', app()->getLocale()) : '-' }}
                </div>
            </div>

            <div>
                <div class="text-xs text-slate-500 uppercase">{{ __('admin.coordinates') }}</div>
                <div class="mt-1 text-slate-900 font-medium">{{ $businessAccount->x }}, {{ $businessAccount->y }}</div>
            </div>

            @if(($businessAccount->status->value ?? $businessAccount->status) === 'pending')
                <div class="pt-2">
                    <div class="flex flex-wrap gap-2">
                        @can('approve business accounts')
                            <form method="POST" action="{{ route('admin.business-accounts.accept', $businessAccount) }}">
                                @csrf
                                <input type="hidden" name="back_to_show" value="1">
                                <button type="submit" class="inline-flex items-center px-3 py-1.5 rounded-lg bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-xs font-semibold">
                                    {{ __('admin.accept') }}
                                </button>
                            </form>
                        @endcan
                        @can('reject business accounts')
                            <form method="POST" action="{{ route('admin.business-accounts.reject', $businessAccount) }}">
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
            @php $images = $businessAccount->getMedia('images'); @endphp
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
            <h2 class="text-lg font-semibold text-slate-900">{{ __('admin.documents') }}</h2>
            @php $documents = $businessAccount->getMedia('documents'); @endphp
            @if($documents->isEmpty())
                <p class="text-sm text-slate-500">{{ __('admin.no_documents') }}</p>
            @else
                <div class="space-y-2">
                    @foreach($documents as $document)
                        @php
                            $documentUrl = url('storage/'.$document->getPathRelativeToRoot());
                        @endphp
                        <a href="{{ $documentUrl }}" download class="inline-flex items-center px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-semibold">
                            {{ $document->file_name }}
                        </a>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6 space-y-4">
            <div class="flex items-center justify-between gap-3">
                <h2 class="text-lg font-semibold text-slate-900">{{ __('admin.location_map') }}</h2>
                @if(is_numeric($businessAccount->x) && is_numeric($businessAccount->y))
                    <span class="inline-flex items-center rounded-full bg-indigo-50 px-3 py-1 text-xs font-semibold text-indigo-700">
                        {{ number_format((float) $businessAccount->x, 5) }}, {{ number_format((float) $businessAccount->y, 5) }}
                    </span>
                @endif
            </div>
            @php
                $hasCoordinates = is_numeric($businessAccount->x) && is_numeric($businessAccount->y);
            @endphp
            @if($hasCoordinates)
                @php
                    $x = (float) $businessAccount->x;
                    $y = (float) $businessAccount->y;

                    $latitude = $x;
                    $longitude = $y;

                    $primaryValid = abs($latitude) <= 90 && abs($longitude) <= 180;
                    $swappedValid = abs($y) <= 90 && abs($x) <= 180;

                    if (! $primaryValid && $swappedValid) {
                        $latitude = $y;
                        $longitude = $x;
                    }

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
                        title="Business account location map"
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
                        Open in OpenStreetMap
                    </a>
                </div>
            @else
                <p class="text-sm text-slate-500">{{ __('admin.coordinates_unavailable') }}</p>
            @endif
        </div>
    </div>
@endsection

