@extends('layouts.admin')

@section('title', __('admin.manage_users'))

@section('content')
    <div class="space-y-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
            <h1 class="text-2xl font-semibold text-slate-900">{{ __('admin.app_users') }}</h1>

            @can('create user')
                <a
                    href="{{ route('admin.app-users.create') }}"
                    class="inline-flex items-center justify-center px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm transition"
                >
                    {{ __('admin.create_user') }}
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
            <div class="p-4">
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider text-xs">
                        <tr>
                            <th class="px-4 py-3 {{ app()->getLocale() === 'ar' ? 'text-right' : 'text-left' }}">
                                {{ __('admin.name') }}
                            </th>
                            <th class="px-4 py-3 {{ app()->getLocale() === 'ar' ? 'text-right' : 'text-left' }}">
                                {{ __('admin.phone') }}
                            </th>
                            <th class="px-4 py-3 {{ app()->getLocale() === 'ar' ? 'text-right' : 'text-left' }}">
                                {{ __('admin.status') }}
                            </th>
                            <th class="px-4 py-3 {{ app()->getLocale() === 'ar' ? 'text-right' : 'text-left' }}">
                                {{ __('admin.actions') }}
                            </th>
                        </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-100">
                        @forelse($appUsers as $appUser)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="px-4 py-3 whitespace-nowrap font-medium text-slate-900">
                                    {{ $appUser->user?->name ?? '-' }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-slate-700">
                                    {{ $appUser->phone }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    @if($appUser->trashed())
                                        <span class="inline-flex items-center rounded-xl border border-amber-200 bg-amber-50 px-3 py-1 text-xs font-semibold text-amber-700">
                                            {{ __('admin.trashed') }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center rounded-xl border border-emerald-200 bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">
                                            {{ __('admin.active') }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        @if(! $appUser->trashed() && auth()->user()?->can('edit user'))
                                            <a
                                                href="{{ route('admin.app-users.edit', $appUser) }}"
                                                class="inline-flex items-center rounded-xl border border-slate-200 bg-slate-50 px-3 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-100 transition"
                                            >
                                                {{ __('admin.edit') }}
                                            </a>
                                        @endif

                                        @if(! $appUser->trashed() && auth()->user()?->can('delete user'))
                                            <form
                                                method="POST"
                                                action="{{ route('admin.app-users.destroy', $appUser) }}"
                                                onsubmit="return confirm('{{ __('admin.confirm_delete') }}')"
                                            >
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="inline-flex items-center rounded-xl border border-rose-200 bg-rose-50 px-3 py-1 text-xs font-semibold text-rose-700 hover:bg-rose-100 transition">
                                                    {{ __('admin.delete') }}
                                                </button>
                                            </form>
                                        @endif

                                        @if($appUser->trashed() && auth()->user()?->can('delete user'))
                                            <form method="POST" action="{{ route('admin.app-users.restore', $appUser->id) }}">
                                                @csrf
                                                <button type="submit" class="inline-flex items-center rounded-xl border border-indigo-200 bg-indigo-50 px-3 py-1 text-xs font-semibold text-indigo-700 hover:bg-indigo-100 transition">
                                                    {{ __('admin.restore') }}
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-12 text-center text-slate-500">
                                    {{ __('admin.no_users') }}
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
