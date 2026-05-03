@extends('layouts.admin')

@section('title', __('api.report_management_title'))

@section('content')
    @php
        $locale = app()->getLocale();
    @endphp

    <div class="space-y-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
            <h1 class="text-2xl font-semibold text-slate-900">{{ __('api.report_management_title') }}</h1>
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
                        <th class="px-4 py-3 {{ $locale === 'ar' ? 'text-right' : 'text-left' }}">{{ __('api.report_reporter_name') }}</th>
                        <th class="px-4 py-3 {{ $locale === 'ar' ? 'text-right' : 'text-left' }}">{{ __('api.report_order_id') }}</th>
                        <th class="px-4 py-3 {{ $locale === 'ar' ? 'text-right' : 'text-left' }}">{{ __('api.report_reason') }}</th>
                        <th class="px-4 py-3 {{ $locale === 'ar' ? 'text-right' : 'text-left' }}">{{ __('api.report_created_at') }}</th>
                        <th class="px-4 py-3 {{ $locale === 'ar' ? 'text-right' : 'text-left' }}">{{ __('api.report_status') }}</th>
                        <th class="px-4 py-3 {{ $locale === 'ar' ? 'text-right' : 'text-left' }}">{{ __('api.report_actions') }}</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                    @forelse($reports as $report)
                        @php
                            $statusValue = $report->status?->value ?? (string) $report->status;
                            $badgeClasses = $statusValue === 'resolved'
                                ? 'bg-emerald-50 text-emerald-700'
                                : 'bg-rose-50 text-rose-700';
                        @endphp
                        <tr class="hover:bg-slate-50/80 transition align-top">
                            <td class="px-4 py-3 font-medium text-slate-900">
                                {{ $report->user?->name ?? '-' }}
                            </td>
                            <td class="px-4 py-3 text-slate-700">
                                #{{ $report->order_id }}
                            </td>
                            <td class="px-4 py-3 text-slate-700 max-w-xs">
                                <div class="line-clamp-2 break-words">
                                    {{ \Illuminate\Support\Str::limit($report->reason, 90) }}
                                </div>
                            </td>
                            <td class="px-4 py-3 text-slate-700 whitespace-nowrap">
                                {{ $report->created_at?->format('Y-m-d h:i A') ?? '-' }}
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold {{ $badgeClasses }}">
                                    {{ $statusValue === 'resolved' ? __('api.report_status_resolved') : __('api.report_status_pending') }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex flex-wrap gap-2">
                                    <a href="{{ route('admin.reports.show', $report) }}" class="inline-flex items-center px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-semibold">
                                        {{ __('api.report_view_details') }}
                                    </a>
                                    @if($statusValue === 'pending')
                                        @can('resolve reports')
                                            <form method="POST" action="{{ route('admin.reports.resolve', $report) }}">
                                                @csrf
                                                <button type="submit" class="inline-flex items-center px-3 py-1.5 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-xs font-semibold">
                                                    {{ __('api.report_mark_resolved') }}
                                                </button>
                                            </form>
                                        @endcan
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center text-slate-500">
                                {{ __('api.report_empty_state') }}
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            @if($reports->hasPages())
                <div class="border-t border-slate-200 px-4 py-4">
                    {{ $reports->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
