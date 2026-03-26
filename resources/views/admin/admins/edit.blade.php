@extends('layouts.admin')

@section('title', __('admin.edit_admin'))

@section('content')
    <div class="max-w-2xl mx-auto">
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 bg-indigo-50 border-b border-indigo-100">
                <div class="text-lg font-semibold text-indigo-900">{{ __('admin.edit_admin') }}</div>
                <div class="text-sm text-indigo-800/80 mt-1">{{ __('admin.update_admin') }}</div>
            </div>

            <div class="p-6">
                @if($errors->any())
                    <div class="bg-rose-50 border border-rose-200 text-rose-700 rounded-2xl px-4 py-3 text-sm">
                        <ul class="mb-0 list-disc list-inside">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.admins.update', $admin) }}" class="mt-5 space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label for="name" class="block text-sm font-medium text-slate-700">
                            {{ __('admin.name') }}
                        </label>
                        <input
                            type="text"
                            name="name"
                            id="name"
                            value="{{ old('name', $admin->user?->name) }}"
                            required
                            autofocus
                            class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm
                                focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500
                                @error('name') border-rose-400 focus:ring-rose-200 @enderror"
                        >
                        @error('name')
                            <div class="mt-1 text-sm text-rose-600">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-slate-700">
                            {{ __('admin.email') }}
                        </label>
                        <input
                            type="email"
                            name="email"
                            id="email"
                            value="{{ old('email', $admin->email) }}"
                            required
                            class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm
                                focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500
                                @error('email') border-rose-400 focus:ring-rose-200 @enderror"
                        >
                        @error('email')
                            <div class="mt-1 text-sm text-rose-600">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-slate-700">
                            {{ __('admin.password') }}
                        </label>
                        <input
                            type="password"
                            name="password"
                            id="password"
                            class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm
                                focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500
                                @error('password') border-rose-400 focus:ring-rose-200 @enderror"
                        >
                        <p class="mt-1 text-xs text-slate-500">{{ __('admin.password_optional_hint') }}</p>
                        @error('password')
                            <div class="mt-1 text-sm text-rose-600">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-slate-700">
                            {{ __('admin.confirm_password') }}
                        </label>
                        <input
                            type="password"
                            name="password_confirmation"
                            id="password_confirmation"
                            class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm
                                focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500
                                @error('password_confirmation') border-rose-400 focus:ring-rose-200 @enderror"
                        >
                        @error('password_confirmation')
                            <div class="mt-1 text-sm text-rose-600">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700">
                            {{ __('admin.assign_roles') }}
                        </label>
                        <p class="mt-1 text-xs text-slate-500">{{ __('admin.assign_roles_help') }}</p>

                        @if($admin->main_admin)
                            <div class="mt-2 text-xs text-amber-700 bg-amber-50 border border-amber-200 rounded-lg px-3 py-2">
                                {{ __('admin.cannot_edit_main_admin_roles') }}
                            </div>
                        @endif

                        @php
                            $selectedRoleIds = old('role_ids', $assignedRoleIds);
                            $selectedRoleIds = array_map('intval', is_array($selectedRoleIds) ? $selectedRoleIds : []);
                        @endphp

                        <div class="mt-3 grid grid-cols-1 sm:grid-cols-2 gap-2">
                            @foreach($roles as $role)
                                <label class="inline-flex items-center gap-2 rounded-lg border border-slate-200 bg-slate-50 px-3 py-2">
                                    <input
                                        type="checkbox"
                                        name="role_ids[]"
                                        value="{{ $role->id }}"
                                        @checked(in_array((int) $role->id, $selectedRoleIds, true))
                                        @disabled($admin->main_admin)
                                        class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 disabled:opacity-60"
                                    >
                                    <span class="text-sm text-slate-800">{{ $role->name }}</span>
                                </label>
                            @endforeach
                        </div>

                        @if($admin->main_admin)
                            @foreach($selectedRoleIds as $selectedRoleId)
                                <input type="hidden" name="role_ids[]" value="{{ $selectedRoleId }}">
                            @endforeach
                        @endif

                        @error('role_ids')
                            <div class="mt-1 text-sm text-rose-600">{{ $message }}</div>
                        @enderror
                        @error('role_ids.*')
                            <div class="mt-1 text-sm text-rose-600">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="flex flex-col sm:flex-row gap-3 sm:items-center sm:justify-between">
                        <a
                            href="{{ route('admin.admins.index') }}"
                            class="inline-flex items-center justify-center px-4 py-2.5 rounded-xl border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 font-semibold text-sm transition"
                        >
                            {{ __('admin.cancel') }}
                        </a>

                        <button
                            type="submit"
                            class="inline-flex items-center justify-center px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm transition"
                        >
                            {{ __('admin.update_admin') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
