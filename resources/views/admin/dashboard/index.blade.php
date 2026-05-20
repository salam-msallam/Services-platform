@extends('layouts.admin')

@section('title', __('admin.dashboard'))

@section('content')
    <div class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm min-h-56 flex flex-col items-center justify-center text-center">
                <div class="h-14 w-14 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                    <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M4 21V5a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v16" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                        <path d="M16 9h2a2 2 0 0 1 2 2v10" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                        <path d="M8 7h4M8 11h4M8 15h4M4 21h16" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                </div>
                <div class="mt-5 text-4xl font-bold text-slate-900">
                    {{ $totalBusinessAccounts }}
                </div>
                <div class="mt-2 text-sm font-medium text-slate-500">{{ __('admin.total_business_accounts') }}</div>
            </div>

            <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm min-h-56 flex flex-col items-center justify-center text-center">
                <div class="h-14 w-14 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center">
                    <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M9 6V5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v1" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        <path d="M4 7h16v12H4V7Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                        <path d="M4 12h16" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                </div>
                <div class="mt-5 text-4xl font-bold text-slate-900 ">
                    {{ $totalServices }}
                </div>
                <div class="mt-2 text-sm font-medium text-slate-500">{{ __('admin.total_services') }}</div>
            </div>

            <a href="{{ route('admin.business-accounts.index', ['status' => App\Enums\StatusEnum::Pending->value]) }}"
                class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm min-h-56 flex flex-col items-center justify-center text-center transition hover:border-slate-300 hover:shadow-md">
                <div class="h-14 w-14 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center">
                    <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2"/>
                        <path d="M12 7v5l3 2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <div class="mt-5 text-4xl font-bold text-slate-900">
                    {{ $pendingReviewsCount }}
                </div>
                <div class="mt-2 text-sm font-medium text-slate-500">{{ __('admin.pending_reviews') }}</div>
            </a>

            <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm min-h-56 flex flex-col items-center justify-center text-center">
                <div class="h-14 w-14 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M6 6h15l-2 8H8L6 6Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                        <path d="M6 6 5 3H2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <circle cx="9" cy="20" r="1" stroke="currentColor" stroke-width="2"/>
                        <circle cx="18" cy="20" r="1" stroke="currentColor" stroke-width="2"/>
                    </svg>
                </div>
                <div class="mt-5 text-4xl font-bold text-slate-900">
                    {{ $totalOrders }}
                </div>
                <div class="mt-2 text-sm font-medium text-slate-500">{{ __('admin.orders_overview') }}</div>

                <div class="mt-4 flex flex-wrap justify-center gap-2">
                    <span class="inline-flex items-center rounded-lg bg-orange-50 px-2.5 py-1 text-xs font-semibold text-orange-700 border border-orange-200">
                        {{ __('admin.pending') }}: {{ $pendingOrdersCount }}
                    </span>
                    <span class="inline-flex items-center rounded-lg bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700 border border-emerald-200">
                        {{ __('admin.accepted') }}: {{ $acceptedOrdersCount }}
                    </span>
                    <span class="inline-flex items-center rounded-lg bg-rose-50 px-2.5 py-1 text-xs font-semibold text-rose-700 border border-rose-200">
                        {{ __('admin.rejected') }}: {{ $rejectedOrdersCount }}
                    </span>
                </div>
            </div>
        </div>

        @if($currentSlider !== null)
            @php($currentSliderImageUrl = $currentSlider->getFirstMediaUrl('scroll_bar_image'))
            <div class="mt-8 max-w-6xl mx-auto bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div class="min-w-0">
                        <span class="inline-flex rounded-full bg-indigo-50 px-3 py-1 text-xs font-semibold text-indigo-700 border border-indigo-100">
                            {{ __('admin.currently_displayed_slider') }}
                        </span>
                        <h2 class="mt-2 text-lg font-semibold text-slate-900 truncate">
                            {{ $currentSlider->getTranslation('title', app()->getLocale(), false) }}
                        </h2>
                    </div>

                    @can('view-sliders')
                        <a
                            href="{{ route('admin.sliders.index') }}"
                            class="inline-flex shrink-0 items-center justify-center px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm transition"
                        >
                            {{ __('admin.manage_sliders') }}
                        </a>
                    @endcan
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-[minmax(0,1.7fr)_minmax(280px,0.8fr)]">
                    @if($currentSliderImageUrl !== '')
                        <img
                            src="{{ $currentSliderImageUrl }}"
                            alt="{{ $currentSlider->getTranslation('title', app()->getLocale(), false) }}"
                            class="h-56 w-full object-cover sm:h-64 lg:h-72"
                        >
                    @else
                        <div class="h-56 sm:h-64 lg:h-72 bg-slate-100"></div>
                    @endif

                    <div class="p-5 bg-slate-50/70 flex flex-col justify-center border-t lg:border-t-0 lg:border-l border-slate-100">
                        <dl class="grid grid-cols-2 gap-3 text-sm">
                            <div class="rounded-xl bg-white border border-slate-200 p-3">
                                <dt class="text-xs font-medium text-slate-500">{{ __('admin.status') }}</dt>
                                <dd class="mt-1 font-semibold text-emerald-700">{{ __('admin.active') }}</dd>
                            </div>
                            <div class="rounded-xl bg-white border border-slate-200 p-3">
                                <dt class="text-xs font-medium text-slate-500">{{ __('admin.created_at') }}</dt>
                                <dd class="mt-1 font-semibold text-slate-900">{{ $currentSlider->created_at?->format('Y-m-d') }}</dd>
                            </div>
                        </dl>

                        <div class="mt-4 rounded-xl bg-white border border-slate-200 p-3">
                            <div class="text-xs font-medium text-slate-500">{{ __('admin.display') }}</div>
                            <div class="mt-1 text-sm font-semibold text-slate-900">{{ __('admin.currently_displayed') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6">
            <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-semibold text-slate-900">
                        {{ __('admin.welcome') }}, {{ auth()->user()->name }}
                    </h2>

                    @if (auth()->user()->admin)
                        <p class="mt-2 text-sm text-slate-600">
                            {{ auth()->user()->admin->email }}
                        </p>
                        @if (auth()->user()->admin->main_admin)
                            <p
                                class="mt-2 inline-flex items-center gap-2 rounded-xl bg-emerald-50 text-emerald-700 border border-emerald-200 px-3 py-1.5 text-sm">
                                {{ __('admin.you_are_main') }}
                            </p>
                        @endif
                    @endif
                </div>

                <div class="flex flex-wrap gap-3">
                    @can('manage admins')
                        <a href="{{ route('admin.admins.index') }}"
                            class="inline-flex items-center px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm transition">
                            {{ __('admin.manage_admins') }}
                        </a>
                    @endcan

                    <a href="{{ route('admin.dashboard') }}"
                        class="inline-flex items-center px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 font-semibold text-sm transition border border-slate-200">
                        {{ __('admin.dashboard') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
