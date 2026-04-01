@extends('layouts.admin')

@section('title', __('admin.manage_sub_categories'))

@section('content')
    <div class="space-y-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
            <h1 class="text-2xl font-semibold text-slate-900">{{ __('admin.sub_categories') }}</h1>
            <a
                href="{{ route('admin.sub-categories.create') }}"
                class="inline-flex items-center justify-center px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm transition"
            >
                {{ __('admin.create_sub_category') }}
            </a>
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
            <div class="p-4 overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider text-xs">
                    <tr>
                        <th class="px-4 py-3 {{ app()->getLocale() === 'ar' ? 'text-right' : 'text-left' }}">{{ __('admin.name_ar') }}</th>
                        <th class="px-4 py-3 {{ app()->getLocale() === 'ar' ? 'text-right' : 'text-left' }}">{{ __('admin.name_en') }}</th>
                        <th class="px-4 py-3 {{ app()->getLocale() === 'ar' ? 'text-right' : 'text-left' }}">{{ __('admin.parent_category_ar') }}</th>
                        <th class="px-4 py-3 {{ app()->getLocale() === 'ar' ? 'text-right' : 'text-left' }}">{{ __('admin.parent_category_en') }}</th>
                        <th class="px-4 py-3 {{ app()->getLocale() === 'ar' ? 'text-right' : 'text-left' }}">{{ __('admin.fields_count') }}</th>
                        <th class="px-4 py-3 {{ app()->getLocale() === 'ar' ? 'text-right' : 'text-left' }}">{{ __('admin.dynamic_fields') }}</th>
                        <th class="px-4 py-3 {{ app()->getLocale() === 'ar' ? 'text-right' : 'text-left' }}">{{ __('admin.actions') }}</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                    @forelse($subCategories as $subCategory)
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
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-4 py-3 font-medium text-slate-900">{{ $subCategory->getTranslation('name', 'ar') }}</td>
                            <td class="px-4 py-3 font-medium text-slate-900">{{ $subCategory->getTranslation('name', 'en') }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ $parent ? $parent->getTranslation('name', 'ar') : '-' }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ $parent ? $parent->getTranslation('name', 'en') : '-' }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ count($dynamicFields) }}</td>
                            <td class="px-4 py-3 text-slate-700">
                                @if($dynamicFields === [])
                                    <span class="text-slate-400">-</span>
                                @else
                                    <div class="space-y-2">
                                        @foreach($dynamicFields as $field)
                                            <div class="rounded-lg bg-slate-50 border border-slate-200 px-3 py-2">
                                                <div class="text-xs text-slate-500">
                                                    {{ __('admin.field_type') }}:
                                                    {{ $fieldTypeLabels[(string) data_get($field, 'type')] ?? data_get($field, 'type') }}
                                                </div>
                                                <div class="text-sm text-slate-900 mt-0.5">
                                                    <span class="font-semibold">AR:</span> {{ data_get($field, 'label.ar', '-') }}
                                                    <span class="mx-1 text-slate-300">|</span>
                                                    <span class="font-semibold">EN:</span> {{ data_get($field, 'label.en', '-') }}
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex flex-wrap gap-2">
                                    <a href="{{ route('admin.sub-categories.show', $subCategory) }}" class="inline-flex items-center px-3 py-1.5 rounded-lg bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-xs font-semibold">
                                        {{ __('admin.view_details') }}
                                    </a>
                                    <a href="{{ route('admin.sub-categories.edit', $subCategory) }}" class="inline-flex items-center px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-semibold">
                                        {{ __('admin.edit') }}
                                    </a>
                                    <form method="POST" action="{{ route('admin.sub-categories.destroy', $subCategory) }}" onsubmit="return confirm('{{ __('admin.confirm_delete') }}')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center px-3 py-1.5 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-semibold">
                                            {{ __('admin.delete') }}
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-12 text-center text-slate-500">
                                {{ __('admin.no_sub_categories') }}
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
