@extends('layouts.admin')

@section('title', __('admin.services_review'))

@section('content')
    <div class="space-y-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
            <h1 class="text-2xl font-semibold text-slate-900">{{ __('admin.services_review') }}</h1>
        </div>

        @php
            $currentStatus = $status ?? null;
            $currentTab = $tab ?? 'active';
            $locale = app()->getLocale();
        @endphp

        <div class="flex flex-wrap gap-2">
            <a
                href="{{ route('admin.services.index', ['tab' => 'active']) }}"
                class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-semibold transition {{ $currentTab === 'active' ? 'bg-indigo-600 text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}"
            >
                {{ __('admin.active_services') }}
            </a>
            <a
                href="{{ route('admin.services.index', ['tab' => 'trashed']) }}"
                class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-semibold transition {{ $currentTab === 'trashed' ? 'bg-indigo-600 text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}"
            >
                {{ __('admin.trashed_services') }}
            </a>
        </div>

        <div class="flex flex-wrap gap-2">
            <a
                href="{{ route('admin.services.index', ['tab' => $currentTab]) }}"
                class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-semibold transition {{ $currentStatus === null ? 'bg-indigo-600 text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}"
            >
                {{ __('admin.all_statuses') }}
            </a>
            <a
                href="{{ route('admin.services.index', ['tab' => $currentTab, 'status' => 'pending']) }}"
                class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-semibold transition {{ $currentStatus === 'pending' ? 'bg-indigo-600 text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}"
            >
                {{ __('admin.pending') }}
            </a>
            <a
                href="{{ route('admin.services.index', ['tab' => $currentTab, 'status' => 'accepted']) }}"
                class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-semibold transition {{ $currentStatus === 'accepted' ? 'bg-indigo-600 text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}"
            >
                {{ __('admin.accepted') }}
            </a>
            <a
                href="{{ route('admin.services.index', ['tab' => $currentTab, 'status' => 'rejected']) }}"
                class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-semibold transition {{ $currentStatus === 'rejected' ? 'bg-indigo-600 text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}"
            >
                {{ __('admin.rejected') }}
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

        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
            <div class="p-4 overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider text-xs">
                    <tr>
                        <th class="px-4 py-3 {{ $locale === 'ar' ? 'text-right' : 'text-left' }}">{{ __('admin.service_title') }}</th>
                        <th class="px-4 py-3 {{ $locale === 'ar' ? 'text-right' : 'text-left' }}">{{ __('admin.business_account') }}</th>
                        <th class="px-4 py-3 {{ $locale === 'ar' ? 'text-right' : 'text-left' }}">{{ __('admin.category') }}</th>
                        <th class="px-4 py-3 {{ $locale === 'ar' ? 'text-right' : 'text-left' }}">{{ __('admin.city') }}</th>
                        <th class="px-4 py-3 {{ $locale === 'ar' ? 'text-right' : 'text-left' }}">{{ __('admin.price') }}</th>
                        <th class="px-4 py-3 {{ $locale === 'ar' ? 'text-right' : 'text-left' }}">{{ __('admin.status') }}</th>
                        <th class="px-4 py-3 {{ $locale === 'ar' ? 'text-right' : 'text-left' }}">{{ __('admin.actions') }}</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                    @forelse($services as $service)
                        @php
                            $title = $service->getTranslation('title', $locale) ?? '-';
                            $accountName = $service->businessAccount?->getTranslation('name', $locale) ?? '-';
                            $categoryName = $service->category?->getTranslation('name', $locale) ?? '-';
                            $cityName = $service->city?->getTranslation('name', $locale) ?? '-';
                            $statusValue = $service->status?->value ?? (string) $service->status;
                        @endphp
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-4 py-3 font-medium text-slate-900">
                                {{ $title }}
                            </td>
                            <td class="px-4 py-3 text-slate-700">
                                {{ $accountName }}
                            </td>
                            <td class="px-4 py-3 text-slate-700">
                                {{ $categoryName }}
                                @if($service->subCategory)
                                    <span class="text-slate-400">/</span>
                                    {{ $service->subCategory->getTranslation('name', $locale) }}
                                @endif
                            </td>
                            <td class="px-4 py-3 text-slate-700">{{ $cityName }}</td>
                            <td class="px-4 py-3 text-slate-700">
                                {{ $service->price }} {{ $service->currency }}
                            </td>
                            <td class="px-4 py-3 text-slate-700">{{ $statusValue }}</td>
                            <td class="px-4 py-3">
                                <div class="flex flex-wrap gap-2">
                                    @if($currentTab === 'active')
                                        <a href="{{ route('admin.services.show', $service) }}" class="inline-flex items-center px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-semibold">
                                            {{ __('admin.view_details') }}
                                        </a>
                                        @if($statusValue === 'pending')
                                            @can('approve services')
                                                <form method="POST" action="{{ route('admin.services.accept', $service) }}">
                                                    @csrf
                                                    <button type="submit" class="inline-flex items-center px-3 py-1.5 rounded-lg bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-xs font-semibold">
                                                        {{ __('admin.accept') }}
                                                    </button>
                                                </form>
                                            @endcan

                                            @can('reject services')
                                                <form method="POST" action="{{ route('admin.services.reject', $service) }}">
                                                    @csrf
                                                    <button type="submit" class="inline-flex items-center px-3 py-1.5 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-semibold">
                                                        {{ __('admin.reject') }}
                                                    </button>
                                                </form>
                                            @endcan
                                        @endif
                                    @else
                                        <span class="inline-flex items-center px-3 py-1.5 rounded-lg bg-slate-100 text-slate-500 text-xs font-semibold">
                                            {{ __('admin.trashed') }}
                                        </span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-12 text-center text-slate-500">
                                {{ __('admin.no_services') }}
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

