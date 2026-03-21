@extends('layouts.admin')

@section('title', __('admin.create_admin'))

@section('content')
    <div class="max-w-2xl mx-auto">
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 bg-indigo-50 border-b border-indigo-100">
                <div class="text-lg font-semibold text-indigo-900">{{ __('admin.create_admin') }}</div>
                <div class="text-sm text-indigo-800/80 mt-1">{{ __('admin.add_admin') }}</div>
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

                <form method="POST" action="{{ route('admin.admins.store') }}" class="mt-5 space-y-4">
                    @csrf

                    <div>
                        <label for="name" class="block text-sm font-medium text-slate-700">
                            {{ __('admin.name') }}
                        </label>
                        <input
                            type="text"
                            name="name"
                            id="name"
                            value="{{ old('name') }}"
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
                            value="{{ old('email') }}"
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
                            required
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
                            required
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
                            href="{{ route('admin.admins.index') }}"
                            class="inline-flex items-center justify-center px-4 py-2.5 rounded-xl border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 font-semibold text-sm transition"
                        >
                            {{ __('admin.cancel') }}
                        </a>

                        <button
                            type="submit"
                            class="inline-flex items-center justify-center px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm transition"
                        >
                            {{ __('admin.create_admin') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
