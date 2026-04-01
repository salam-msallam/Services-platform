@extends('layouts.admin')

@section('title', __('admin.sub_category_details'))

@section('content')
    @php
        $dynamicFields = is_array($subCategory->dynamic_fields) ? $subCategory->dynamic_fields : [];
        $fieldTypeLabels = [
            'text' => __('admin.field_type_text'),
            'number' => __('admin.field_type_number'),
            'checkbox' => __('admin.field_type_checkbox'),
            'dropdown' => __('admin.field_type_dropdown'),
        ];
        $parent = $subCategory->category;
    @endphp

    <div class="max-w-5xl mx-auto space-y-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
            <h1 class="text-2xl font-semibold text-slate-900">{{ __('admin.sub_category_details') }}</h1>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.sub-categories.edit', $subCategory) }}" class="inline-flex items-center px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold">
                    {{ __('admin.edit') }}
                </a>
                <a href="{{ route('admin.sub-categories.index') }}" class="inline-flex items-center px-4 py-2.5 rounded-xl border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 text-sm font-semibold">
                    {{ __('admin.sub_categories') }}
                </a>
            </div>
        </div>

        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6">
            <h2 class="text-sm font-semibold text-slate-500 uppercase tracking-wider mb-3">{{ __('admin.parent_category') }}</h2>
            @if($parent)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <div class="text-xs uppercase tracking-wider text-slate-500">{{ __('admin.name_ar') }}</div>
                        <div class="mt-1 text-base font-semibold text-slate-900">{{ $parent->getTranslation('name', 'ar') }}</div>
                    </div>
                    <div>
                        <div class="text-xs uppercase tracking-wider text-slate-500">{{ __('admin.name_en') }}</div>
                        <div class="mt-1 text-base font-semibold text-slate-900">{{ $parent->getTranslation('name', 'en') }}</div>
                    </div>
                </div>
            @else
                <p class="text-sm text-slate-500">-</p>
            @endif
        </div>

        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <div class="text-xs uppercase tracking-wider text-slate-500">{{ __('admin.sub_category_name_ar') }}</div>
                    <div class="mt-1 text-base font-semibold text-slate-900">{{ $subCategory->getTranslation('name', 'ar') }}</div>
                </div>
                <div>
                    <div class="text-xs uppercase tracking-wider text-slate-500">{{ __('admin.sub_category_name_en') }}</div>
                    <div class="mt-1 text-base font-semibold text-slate-900">{{ $subCategory->getTranslation('name', 'en') }}</div>
                </div>
            </div>
        </div>

        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-slate-900">{{ __('admin.dynamic_fields') }}</h2>
                <span class="text-sm text-slate-500">{{ __('admin.fields_count') }}: {{ count($dynamicFields) }}</span>
            </div>

            @if($dynamicFields === [])
                <div class="mt-4 text-sm text-slate-500">{{ __('admin.no_dynamic_fields_defined') }}</div>
            @else
                <div class="mt-4 space-y-3">
                    @foreach($dynamicFields as $field)
                        <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                            <div class="text-xs text-slate-500">
                                {{ __('admin.field_type') }}:
                                {{ $fieldTypeLabels[(string) data_get($field, 'type')] ?? data_get($field, 'type') }}
                            </div>
                            <div class="mt-2 text-sm text-slate-900">
                                <span class="font-semibold">AR:</span> {{ data_get($field, 'label.ar', '-') }}
                                <span class="mx-2 text-slate-300">|</span>
                                <span class="font-semibold">EN:</span> {{ data_get($field, 'label.en', '-') }}
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
@endsection
