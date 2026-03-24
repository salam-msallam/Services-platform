@extends('layouts.admin')

@section('title', __('admin.edit_city'))

@section('content')
    <div class="max-w-xl mx-auto">
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6">
            <h1 class="text-xl font-semibold text-slate-900">{{ __('admin.edit_city') }}</h1>

            @if($errors->any())
                <div class="mt-4 bg-rose-50 border border-rose-200 text-rose-700 rounded-2xl px-4 py-3 text-sm">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.cities.update', $city) }}" class="mt-5 space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label for="name_ar" class="block text-sm font-medium text-slate-700">{{ __('admin.name_ar') }}</label>
                    <input
                        type="text"
                        name="name[ar]"
                        id="name_ar"
                        value="{{ old('name.ar', $city->getTranslation('name', 'ar')) }}"
                        required
                        class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                    >
                </div>

                <div>
                    <label for="name_en" class="block text-sm font-medium text-slate-700">{{ __('admin.name_en') }}</label>
                    <input
                        type="text"
                        name="name[en]"
                        id="name_en"
                        value="{{ old('name.en', $city->getTranslation('name', 'en')) }}"
                        required
                        class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                    >
                </div>

                <div>
                    <label for="x" class="block text-sm font-medium text-slate-700">{{ __('admin.x_coordinate') }}</label>
                    <input
                        type="number"
                        step="0.0000001"
                        name="x"
                        id="x"
                        value="{{ old('x', $city->x) }}"
                        class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                    >
                </div>

                <div>
                    <label for="y" class="block text-sm font-medium text-slate-700">{{ __('admin.y_coordinate') }}</label>
                    <input
                        type="number"
                        step="0.0000001"
                        name="y"
                        id="y"
                        value="{{ old('y', $city->y) }}"
                        class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                    >
                </div>

                <div class="flex items-center justify-between gap-3">
                    <a href="{{ route('admin.cities.index') }}" class="inline-flex items-center px-4 py-2.5 rounded-xl border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 text-sm font-semibold">
                        {{ __('admin.cancel') }}
                    </a>
                    <button type="submit" class="inline-flex items-center px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold">
                        {{ __('admin.save') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
