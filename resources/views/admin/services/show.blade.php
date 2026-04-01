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
                    <div class="text-xs text-slate-500 uppercase">{{ __('admin.price') }}</div>
                    <div class="mt-1 text-slate-900 font-medium">{{ $service->price }} {{ $service->currency }}</div>
                </div>
                <div>
                    <div class="text-xs text-slate-500 uppercase">{{ __('admin.property_type') }}</div>
                    <div class="mt-1 text-slate-900 font-medium">{{ $service->property_type }}</div>
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
    </div>
@endsection

