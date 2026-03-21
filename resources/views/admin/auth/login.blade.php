@extends('layouts.admin-auth')

@section('title', __('admin.login'))

@section('content')
    <div class="bg-white/80 backdrop-blur rounded-2xl shadow-sm border border-slate-200 p-6 sm:p-8">
        <div class="text-center">
            <div class="flex items-center justify-center gap-3 mb-2">
                <div class="h-10 w-10 rounded-xl bg-indigo-600/90 flex items-center justify-center">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 2l9 5v10l-9 5-9-5V7l9-5Z" stroke="white" stroke-width="2" stroke-linejoin="round"/>
                    </svg>
                </div>
                <div class="text-left">
                    <h1 class="text-lg font-semibold text-slate-900">{{ __('admin.sign_in_account') }}</h1>
                    <p class="text-sm text-slate-500">{{ __('admin.sign_in_continue') }}</p>
                </div>
            </div>

            @if($errors->any())
                <div class="mt-4 bg-rose-50 border border-rose-200 text-rose-700 rounded-xl p-3 text-sm text-center">
                    <ul class="mb-0 list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>

        <form class="mt-6 space-y-4" method="POST" action="{{ route('admin.login.post') }}">
            @csrf

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
                    autofocus
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

            <div class="flex items-center justify-between gap-4">
                <label class="flex items-center gap-2 text-sm text-slate-700 cursor-pointer">
                    <input
                        type="checkbox"
                        name="remember"
                        id="remember"
                        value="1"
                        class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"
                        {{ old('remember') ? 'checked' : '' }}
                    >
                    {{ __('admin.remember_me') }}
                </label>
            </div>

            <button
                type="submit"
                class="w-full inline-flex justify-center items-center rounded-2xl bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold px-4 py-2.5 transition"
            >
                {{ __('admin.login') }}
            </button>
        </form>

        <div class="mt-6 text-center text-xs text-slate-500">
            {{ __('admin.all_rights') }}
        </div>
    </div>
@endsection

