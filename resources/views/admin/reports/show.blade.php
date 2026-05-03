@extends('layouts.admin')

@section('title', __('api.report_details_title'))

@section('content')
    @php
        $statusValue = $report->status?->value ?? (string) $report->status;
        $badgeClasses = $statusValue === 'resolved'
            ? 'bg-emerald-50 text-emerald-700'
            : 'bg-rose-50 text-rose-700';
        $businessAccount = $report->order?->businessAccount;
        $businessOwner = $businessAccount?->user;
        $service = $report->order?->service;
        $orderStatusValue = $report->order?->status?->value ?? (string) ($report->order?->status ?? '');
        $locale = app()->getLocale();
    @endphp

    <div class="space-y-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
            <h1 class="text-2xl font-semibold text-slate-900">{{ __('api.report_details_title') }}</h1>
            <a href="{{ route('admin.reports.index') }}" class="inline-flex items-center px-4 py-2.5 rounded-xl border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 text-sm font-semibold">
                {{ __('api.report_back_to_list') }}
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
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                <div>
                    <div class="text-xs text-slate-500 uppercase">{{ __('api.report_status') }}</div>
                    <span class="mt-2 inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold {{ $badgeClasses }}">
                        {{ $statusValue === 'resolved' ? __('api.report_status_resolved') : __('api.report_status_pending') }}
                    </span>
                </div>

                @if($statusValue === 'pending')
                    @can('resolve reports')
                        <form method="POST" action="{{ route('admin.reports.resolve', $report) }}">
                            @csrf
                            <input type="hidden" name="back_to_show" value="1">
                            <button type="submit" class="inline-flex items-center px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold transition">
                                {{ __('api.report_mark_resolved') }}
                            </button>
                        </form>
                    @endcan
                @endif
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <div class="text-xs text-slate-500 uppercase">{{ __('api.report_reporter_name') }}</div>
                    <div class="mt-1 text-slate-900 font-medium">{{ $report->user?->name ?? '-' }}</div>
                </div>
                <div>
                    <div class="text-xs text-slate-500 uppercase">{{ __('api.report_created_at') }}</div>
                    <div class="mt-1 text-slate-900 font-medium">{{ $report->created_at?->format('Y-m-d h:i A') ?? '-' }}</div>
                </div>
                <div>
                    <div class="text-xs text-slate-500 uppercase">{{ __('api.report_order_id') }}</div>
                    <div class="mt-1 text-slate-900 font-medium">#{{ $report->order_id }}</div>
                </div>
                <div>
                    <div class="text-xs text-slate-500 uppercase">{{ __('api.report_order_status') }}</div>
                    <div class="mt-1 text-slate-900 font-medium">{{ $orderStatusValue !== '' ? $orderStatusValue : '-' }}</div>
                </div>
                <div>
                    <div class="text-xs text-slate-500 uppercase">{{ __('api.report_business_account') }}</div>
                    <div class="mt-1 text-slate-900 font-medium">
                        {{ $businessAccount?->getTranslation('name', $locale) ?? '-' }}
                    </div>
                </div>
                <div>
                    <div class="text-xs text-slate-500 uppercase">{{ __('api.report_business_owner') }}</div>
                    <div class="mt-1 text-slate-900 font-medium">{{ $businessOwner?->name ?? '-' }}</div>
                </div>
                <div>
                    <div class="text-xs text-slate-500 uppercase">{{ __('api.report_service') }}</div>
                    <div class="mt-1 text-slate-900 font-medium">
                        {{ $service?->getTranslation('title', $locale) ?? '-' }}
                    </div>
                </div>
                <div>
                    <div class="text-xs text-slate-500 uppercase">{{ __('api.report_quantity') }}</div>
                    <div class="mt-1 text-slate-900 font-medium">{{ $report->order?->quantity ?? '-' }}</div>
                </div>
                <div>
                    <div class="text-xs text-slate-500 uppercase">{{ __('api.report_date_of_need') }}</div>
                    <div class="mt-1 text-slate-900 font-medium">
                        {{ $report->order?->date_of_need?->format('Y-m-d') ?? '-' }}
                    </div>
                </div>
                <div>
                    <div class="text-xs text-slate-500 uppercase">{{ __('api.report_time_of_need') }}</div>
                    <div class="mt-1 text-slate-900 font-medium">{{ $report->order?->time_of_need ?? '-' }}</div>
                </div>
            </div>
        </div>

        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6 space-y-3">
            <h2 class="text-lg font-semibold text-slate-900">{{ __('api.report_reason_full') }}</h2>
            <p class="text-sm leading-7 text-slate-700 whitespace-pre-line break-words">{{ $report->reason }}</p>
        </div>

        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6 space-y-3">
            <h2 class="text-lg font-semibold text-slate-900">{{ __('api.report_order_details') }}</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <div class="text-xs text-slate-500 uppercase">{{ __('api.report_order_id') }}</div>
                    <div class="mt-1 text-slate-900 font-medium">#{{ $report->order_id }}</div>
                </div>
                <div>
                    <div class="text-xs text-slate-500 uppercase">{{ __('api.report_order_status') }}</div>
                    <div class="mt-1 text-slate-900 font-medium">{{ $orderStatusValue !== '' ? $orderStatusValue : '-' }}</div>
                </div>
                <div>
                    <div class="text-xs text-slate-500 uppercase">{{ __('api.report_business_account') }}</div>
                    <div class="mt-1 text-slate-900 font-medium">
                        {{ $businessAccount?->getTranslation('name', $locale) ?? '-' }}
                    </div>
                </div>
                <div>
                    <div class="text-xs text-slate-500 uppercase">{{ __('api.report_business_owner') }}</div>
                    <div class="mt-1 text-slate-900 font-medium">{{ $businessOwner?->name ?? '-' }}</div>
                </div>
            </div>
        </div>
    </div>
@endsection
