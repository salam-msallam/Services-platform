@extends('layouts.admin')

@section('title', __('admin.manage_admins'))

@section('content')
    <div class="space-y-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
            <h1 class="text-2xl font-semibold text-slate-900">{{ __('admin.administrators') }}</h1>

            @can('manage admins')
                <a
                    href="{{ route('admin.admins.create') }}"
                    class="inline-flex items-center justify-center px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm transition"
                >
                    {{ __('admin.add_admin') }}
                </a>
            @endcan
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
            <div class="px-4 py-3 bg-indigo-50 border-b border-indigo-100">
                <div class="text-sm font-semibold text-indigo-900">{{ __('admin.navigation') }}</div>
            </div>

            <div class="p-4">
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider text-xs">
                        <tr>
                            <th class="px-4 py-3 {{ app()->getLocale() === 'ar' ? 'text-right' : 'text-left' }}">
                                {{ __('admin.name') }}
                            </th>
                            <th class="px-4 py-3 {{ app()->getLocale() === 'ar' ? 'text-right' : 'text-left' }}">
                                {{ __('admin.email') }}
                            </th>
                            <th class="px-4 py-3 {{ app()->getLocale() === 'ar' ? 'text-right' : 'text-left' }}">
                                {{ __('admin.main_admin') }}
                            </th>
                            <th class="px-4 py-3 {{ app()->getLocale() === 'ar' ? 'text-right' : 'text-left' }}">
                                {{ __('admin.actions') }}
                            </th>
                        </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-100">
                        @forelse($admins as $admin)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="px-4 py-3 whitespace-nowrap font-medium text-slate-900">
                                    {{ $admin->user->name }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-slate-700">
                                    {{ $admin->email }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    @if($admin->main_admin)
                                        <span class="inline-flex items-center rounded-xl border border-emerald-200 bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">
                                            {{ __('admin.yes') }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center rounded-xl border border-slate-200 bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700">
                                            {{ __('admin.no') }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    @if(auth()->id() !== $admin->user_id && ! $admin->main_admin)
                                        <form
                                            method="POST"
                                            action="{{ route('admin.admins.destroy', $admin) }}"
                                            onsubmit="return confirm('{{ __('admin.confirm_delete') }}')"
                                        >
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex items-center rounded-xl border border-rose-200 bg-rose-50 px-3 py-1 text-xs font-semibold text-rose-700 hover:bg-rose-100 transition">
                                                {{ __('admin.delete') }}
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-xs text-slate-400">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-12 text-center text-slate-500">
                                    {{ __('admin.no_admins') }}
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
