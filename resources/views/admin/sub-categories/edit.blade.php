@extends('layouts.admin')

@section('title', __('admin.edit_sub_category'))

@section('content')
    <div class="max-w-4xl mx-auto">
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6">
            <h1 class="text-xl font-semibold text-slate-900">{{ __('admin.edit_sub_category') }}</h1>

            @if($errors->any())
                <div class="mt-4 bg-rose-50 border border-rose-200 text-rose-700 rounded-2xl px-4 py-3 text-sm">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form
                method="POST"
                action="{{ route('admin.sub-categories.update', $subCategory) }}"
                class="mt-5 space-y-5"
            >
                @csrf
                @method('PUT')

                <div>
                    <label for="category_id" class="block text-sm font-medium text-slate-700">{{ __('admin.parent_category') }}</label>
                    <select
                        name="category_id"
                        id="category_id"
                        required
                        class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                    >
                        <option value="">{{ __('admin.select_parent_category') }}</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" @selected((string) old('category_id', $subCategory->category_id) === (string) $category->id)>
                                {{ $category->getTranslation('name', 'ar') }} / {{ $category->getTranslation('name', 'en') }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="name_ar" class="block text-sm font-medium text-slate-700">{{ __('admin.name_ar') }}</label>
                        <input
                            type="text"
                            name="name[ar]"
                            id="name_ar"
                            value="{{ old('name.ar', $subCategory->getTranslation('name', 'ar')) }}"
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
                            value="{{ old('name.en', $subCategory->getTranslation('name', 'en')) }}"
                            required
                            class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                        >
                    </div>
                </div>

                <div class="border border-slate-200 rounded-2xl p-4">
                    <div class="flex items-center justify-between gap-3">
                        <h2 class="text-base font-semibold text-slate-900">{{ __('admin.dynamic_fields') }}</h2>
                        <button
                            type="button"
                            id="add-field-button"
                            class="inline-flex items-center px-3 py-2 rounded-xl border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 text-sm font-semibold"
                        >
                            {{ __('admin.add_field') }}
                        </button>
                    </div>

                    <p class="mt-1 text-xs text-slate-500">{{ __('admin.dynamic_fields_hint') }}</p>

                    <div id="dynamic-fields-container" class="mt-4 space-y-3"></div>

                    <div id="no-dynamic-fields-message" class="mt-4 text-sm text-slate-500">
                        {{ __('admin.no_dynamic_fields_defined') }}
                    </div>
                </div>

                <div class="flex items-center justify-between gap-3">
                    <a href="{{ route('admin.sub-categories.index') }}" class="inline-flex items-center px-4 py-2.5 rounded-xl border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 text-sm font-semibold">
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

@section('scripts')
    @include('admin.partials.dynamic-fields-script', ['initialFields' => old('dynamic_fields', $subCategory->dynamic_fields ?? [])])
@endsection
