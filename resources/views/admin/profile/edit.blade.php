@extends('layouts.admin')

@section('title', __('admin.profile'))

@section('content')
    <div class="max-w-2xl mx-auto">
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 bg-indigo-50 border-b border-indigo-100">
                <div class="text-lg font-semibold text-indigo-900">{{ __('admin.profile') }}</div>
                <div class="text-sm text-indigo-800/80 mt-1">{{ __('admin.profile_help') }}</div>
            </div>

            <div class="p-6">
                @if(session('success'))
                    <div class="mb-5 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-2xl px-4 py-3 text-sm">
                        {{ session('success') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="mb-5 bg-rose-50 border border-rose-200 text-rose-700 rounded-2xl px-4 py-3 text-sm">
                        <ul class="mb-0 list-disc list-inside">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 rounded-2xl border border-slate-200 bg-slate-50 p-4">
                    <div>
                        <div class="text-xs font-semibold uppercase text-slate-500">{{ __('admin.name') }}</div>
                        <div class="mt-1 text-sm text-slate-900">{{ $user->name }}</div>
                    </div>
                    <div>
                        <div class="text-xs font-semibold uppercase text-slate-500">{{ __('admin.email') }}</div>
                        <div class="mt-1 text-sm text-slate-900">{{ $user->admin?->email }}</div>
                    </div>
                </div>

                <form method="POST" action="{{ route('admin.profile.update') }}" class="mt-6 space-y-4">
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
                            value="{{ old('name', $user->name) }}"
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
                            value="{{ old('email', $user->admin?->email) }}"
                            required
                            class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm
                                focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500
                                @error('email') border-rose-400 focus:ring-rose-200 @enderror"
                        >
                        @error('email')
                            <div class="mt-1 text-sm text-rose-600">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="border-t border-slate-200 pt-4">
                        <div class="text-sm font-semibold text-slate-900">{{ __('admin.change_password') }}</div>
                        <p class="mt-1 text-xs text-slate-500">{{ __('admin.profile_password_hint') }}</p>
                    </div>

                    <div>
                        <label for="current_password" class="block text-sm font-medium text-slate-700">
                            {{ __('admin.current_password') }}
                        </label>
                        <input
                            type="password"
                            name="current_password"
                            id="current_password"
                            autocomplete="current-password"
                            class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm
                                focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500
                                @error('current_password') border-rose-400 focus:ring-rose-200 @enderror"
                        >
                        @error('current_password')
                            <div class="mt-1 text-sm text-rose-600">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-slate-700">
                            {{ __('admin.new_password') }}
                        </label>
                        <input
                            type="password"
                            name="password"
                            id="password"
                            autocomplete="new-password"
                            class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm
                                focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500
                                @error('password') border-rose-400 focus:ring-rose-200 @enderror"
                        >
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
                            autocomplete="new-password"
                            class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm
                                focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500
                                @error('password_confirmation') border-rose-400 focus:ring-rose-200 @enderror"
                        >
                        @error('password_confirmation')
                            <div class="mt-1 text-sm text-rose-600">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="flex flex-col sm:flex-row gap-3 sm:items-center sm:justify-between">
                        <a
                            href="{{ route('admin.dashboard') }}"
                            class="inline-flex items-center justify-center px-4 py-2.5 rounded-xl border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 font-semibold text-sm transition"
                        >
                            {{ __('admin.cancel') }}
                        </a>

                        <button
                            type="submit"
                            class="inline-flex items-center justify-center px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm transition"
                        >
                            {{ __('admin.save_profile') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
