@extends('layouts.admin')

@section('title', __('admin.dashboard'))

@section('content')
    <div class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
                <div class="text-sm text-slate-500">{{ __('admin.administrators') }}</div>
                <div class="mt-2 text-2xl font-semibold text-slate-900">
                    {{ auth()->user()?->admin ? 1 : 0 }}
                </div>
                <div class="mt-1 text-sm text-slate-500">
                    {{ __('admin.yes') }} / {{ __('admin.no') }}
                </div>
            </div>

            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
                <div class="text-sm text-slate-500">{{ __('admin.main_admin') }}</div>
                <div class="mt-2 text-2xl font-semibold text-slate-900">
                    @if(auth()->user()?->admin && auth()->user()->admin->main_admin)
                        {{ __('admin.yes') }}
                    @else
                        {{ __('admin.no') }}
                    @endif
                </div>
                <div class="mt-1 text-sm text-slate-500">{{ __('admin.account') }}</div>
            </div>

            <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
                <div class="text-sm text-slate-500">{{ __('admin.actions') }}</div>
                <div class="mt-2 text-2xl font-semibold text-slate-900">SaaS</div>
                <div class="mt-1 text-sm text-slate-500">Slate + Indigo</div>
            </div>
        </div>

        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6">
            <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-semibold text-slate-900">
                        {{ __('admin.welcome') }}, {{ auth()->user()->name }}
                    </h2>

                    @if(auth()->user()->admin)
                        <p class="mt-2 text-sm text-slate-600">
                            {{ auth()->user()->admin->email }}
                        </p>
                        @if(auth()->user()->admin->main_admin)
                            <p class="mt-2 inline-flex items-center gap-2 rounded-xl bg-emerald-50 text-emerald-700 border border-emerald-200 px-3 py-1.5 text-sm">
                                {{ __('admin.you_are_main') }}
                            </p>
                        @endif
                    @endif
                </div>

                <div class="flex flex-wrap gap-3">
                    @can('manage admins')
                        <a
                            href="{{ route('admin.admins.index') }}"
                            class="inline-flex items-center px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm transition"
                        >
                            {{ __('admin.manage_admins') }}
                        </a>
                    @endcan

                    <a
                        href="{{ route('admin.dashboard') }}"
                        class="inline-flex items-center px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 font-semibold text-sm transition border border-slate-200"
                    >
                        {{ __('admin.dashboard') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection

