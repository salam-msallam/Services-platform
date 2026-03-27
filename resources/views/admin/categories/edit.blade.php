@extends('layouts.admin')

@section('title', __('admin.edit_category'))

@section('content')
    <div class="max-w-4xl mx-auto">
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6">
            <h1 class="text-xl font-semibold text-slate-900">{{ __('admin.edit_category') }}</h1>

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
                action="{{ route('admin.categories.update', $category) }}"
                class="mt-5 space-y-5"
            >
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="name_ar" class="block text-sm font-medium text-slate-700">{{ __('admin.name_ar') }}</label>
                        <input
                            type="text"
                            name="name[ar]"
                            id="name_ar"
                            value="{{ old('name.ar', $category->getTranslation('name', 'ar')) }}"
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
                            value="{{ old('name.en', $category->getTranslation('name', 'en')) }}"
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
                    <a href="{{ route('admin.categories.index') }}" class="inline-flex items-center px-4 py-2.5 rounded-xl border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 text-sm font-semibold">
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
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const addButton = document.getElementById('add-field-button');
            const fieldsContainer = document.getElementById('dynamic-fields-container');
            const emptyMessage = document.getElementById('no-dynamic-fields-message');
            const oldFields = @json(old('dynamic_fields', $category->dynamic_fields ?? []));

            const fieldTypes = [
                { value: 'text', label: @json(__('admin.field_type_text')) },
                { value: 'number', label: @json(__('admin.field_type_number')) },
                { value: 'checkbox', label: @json(__('admin.field_type_checkbox')) },
                { value: 'dropdown', label: @json(__('admin.field_type_dropdown')) },
            ];

            const updateEmptyState = function () {
                emptyMessage.classList.toggle('hidden', fieldsContainer.children.length > 0);
            };

            const reindexFieldNames = function () {
                const fieldRows = fieldsContainer.querySelectorAll('[data-field-row]');

                fieldRows.forEach(function (row, index) {
                    row.querySelector('[data-input-label-ar]').name = `dynamic_fields[${index}][label][ar]`;
                    row.querySelector('[data-input-label-en]').name = `dynamic_fields[${index}][label][en]`;
                    row.querySelector('[data-input-type]').name = `dynamic_fields[${index}][type]`;
                    row.querySelector('[data-remove-button]').setAttribute('aria-label', `{{ __('admin.remove_field') }} ${index + 1}`);
                });
            };

            const createTypeOptions = function (selectedType) {
                return fieldTypes.map(function (type) {
                    const selected = selectedType === type.value ? 'selected' : '';
                    return `<option value="${type.value}" ${selected}>${type.label}</option>`;
                }).join('');
            };

            const addField = function (field) {
                const row = document.createElement('div');
                row.setAttribute('data-field-row', '');
                row.className = 'grid grid-cols-1 md:grid-cols-12 gap-3 border border-slate-200 rounded-xl p-3';
                row.innerHTML = `
                    <div class="md:col-span-4">
                        <label class="block text-xs font-medium text-slate-700">{{ __('admin.field_label_ar') }}</label>
                        <input type="text" data-input-label-ar value="${field.label.ar ?? ''}" required class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div class="md:col-span-4">
                        <label class="block text-xs font-medium text-slate-700">{{ __('admin.field_label_en') }}</label>
                        <input type="text" data-input-label-en value="${field.label.en ?? ''}" required class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div class="md:col-span-3">
                        <label class="block text-xs font-medium text-slate-700">{{ __('admin.field_type') }}</label>
                        <select data-input-type required class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            ${createTypeOptions(field.type ?? 'text')}
                        </select>
                    </div>
                    <div class="md:col-span-1 flex items-end">
                        <button type="button" data-remove-button class="w-full inline-flex items-center justify-center px-3 py-2 rounded-xl border border-rose-200 bg-rose-50 hover:bg-rose-100 text-rose-700 text-sm font-semibold">&times;</button>
                    </div>
                `;

                row.querySelector('[data-remove-button]').addEventListener('click', function () {
                    row.remove();
                    reindexFieldNames();
                    updateEmptyState();
                });

                fieldsContainer.appendChild(row);
                reindexFieldNames();
                updateEmptyState();
            };

            addButton.addEventListener('click', function () {
                addField({ label: { ar: '', en: '' }, type: 'text' });
            });

            if (Array.isArray(oldFields) && oldFields.length > 0) {
                oldFields.forEach(function (field) {
                    addField({
                        label: {
                            ar: field?.label?.ar ?? '',
                            en: field?.label?.en ?? '',
                        },
                        type: field?.type ?? 'text',
                    });
                });
            } else {
                updateEmptyState();
            }
        });
    </script>
@endsection
