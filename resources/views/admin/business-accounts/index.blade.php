@extends('layouts.admin')

@section('title', __('admin.business_accounts_review'))

@section('content')
    <div class="space-y-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
            <h1 class="text-2xl font-semibold text-slate-900">{{ __('admin.business_accounts_review') }}</h1>
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
                        <th class="px-4 py-3 {{ app()->getLocale() === 'ar' ? 'text-right' : 'text-left' }}">{{ __('admin.name') }}</th>
                        <th class="px-4 py-3 {{ app()->getLocale() === 'ar' ? 'text-right' : 'text-left' }}">{{ __('admin.activity_type') }}</th>
                        <th class="px-4 py-3 {{ app()->getLocale() === 'ar' ? 'text-right' : 'text-left' }}">{{ __('admin.city') }}</th>
                        <th class="px-4 py-3 {{ app()->getLocale() === 'ar' ? 'text-right' : 'text-left' }}">{{ __('admin.license_number') }}</th>
                        <th class="px-4 py-3 {{ app()->getLocale() === 'ar' ? 'text-right' : 'text-left' }}">{{ __('admin.status') }}</th>
                        <th class="px-4 py-3 {{ app()->getLocale() === 'ar' ? 'text-right' : 'text-left' }}">{{ __('admin.actions') }}</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                    @forelse($businessAccounts as $account)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-4 py-3 font-medium text-slate-900">
                                {{ $account->getTranslation('name', app()->getLocale()) }}
                            </td>
                            <td class="px-4 py-3 text-slate-700">
                                @if($account->activityType !== null)
                                    {{ $account->activityType->getTranslation('name', app()->getLocale()) }}
                                @endif
                            </td>
                            <td class="px-4 py-3 text-slate-700">
                                @if($account->city !== null)
                                    {{ $account->city->getTranslation('name', app()->getLocale()) }}
                                @endif
                            </td>
                            <td class="px-4 py-3 text-slate-700">{{ $account->license_number }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ $account->status->value ?? $account->status }}</td>
                            <td class="px-4 py-3">
                                <div class="flex flex-wrap gap-2">
                                    @if($account->status->value === 'pending')
                                        @can('approve business accounts')
                                            <form method="POST" action="{{ route('admin.business-accounts.accept', $account) }}">
                                                @csrf
                                                <button type="submit" class="inline-flex items-center px-3 py-1.5 rounded-lg bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-xs font-semibold">
                                                    {{ __('admin.accept') }}
                                                </button>
                                            </form>
                                        @endcan

                                        @can('reject business accounts')
                                            <form method="POST" action="{{ route('admin.business-accounts.reject', $account) }}">
                                                @csrf
                                                <button type="submit" class="inline-flex items-center px-3 py-1.5 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-semibold">
                                                    {{ __('admin.reject') }}
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
                                {{ __('admin.no_pending_business_accounts') }}
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

