@php
    /** @var array<int, mixed> $initialFields */
    $initialFields = $initialFields ?? [];
@endphp
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const addButton = document.getElementById('add-field-button');
        const fieldsContainer = document.getElementById('dynamic-fields-container');
        const emptyMessage = document.getElementById('no-dynamic-fields-message');

        if (!addButton || !fieldsContainer || !emptyMessage) {
            return;
        }

        const oldFields = @json($initialFields);

        const fieldTypes = [
            { value: 'text', label: @json(__('admin.field_type_text')) },
            { value: 'number', label: @json(__('admin.field_type_number')) },
            { value: 'checkbox', label: @json(__('admin.field_type_checkbox')) },
            { value: 'dropdown', label: @json(__('admin.field_type_dropdown')) },
        ];

        const escAttr = function (value) {
            return String(value ?? '')
                .replace(/&/g, '&amp;')
                .replace(/"/g, '&quot;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;');
        };

        const optionsToText = function (field) {
            if (field && Array.isArray(field.options)) {
                return field.options.filter(function (o) {
                    return typeof o === 'string' && o.trim() !== '';
                }).join('\n');
            }

            return '';
        };

        const updateEmptyState = function () {
            emptyMessage.classList.toggle('hidden', fieldsContainer.children.length > 0);
        };

        const toggleOptionsRow = function (row) {
            const select = row.querySelector('[data-input-type]');
            const panel = row.querySelector('[data-options-panel]');

            if (!select || !panel) {
                return;
            }

            const isDropdown = select.value === 'dropdown';
            panel.classList.toggle('hidden', !isDropdown);
        };

        const reindexFieldNames = function () {
            const fieldRows = fieldsContainer.querySelectorAll('[data-field-row]');

            fieldRows.forEach(function (row, index) {
                row.querySelector('[data-input-key]').name = 'dynamic_fields[' + index + '][key]';
                row.querySelector('[data-input-label-ar]').name = 'dynamic_fields[' + index + '][label][ar]';
                row.querySelector('[data-input-label-en]').name = 'dynamic_fields[' + index + '][label][en]';
                row.querySelector('[data-input-type]').name = 'dynamic_fields[' + index + '][type]';
                row.querySelector('[data-input-options-text]').name = 'dynamic_fields[' + index + '][options_text]';
                row.querySelector('[data-remove-button]').setAttribute(
                    'aria-label',
                    @json(__('admin.remove_field')) + ' ' + (index + 1)
                );
            });
        };

        const createTypeOptions = function (selectedType) {
            return fieldTypes.map(function (type) {
                const selected = selectedType === type.value ? 'selected' : '';

                return '<option value="' + type.value + '" ' + selected + '>' + type.label + '</option>';
            }).join('');
        };

        const addField = function (field) {
            const type = field.type ?? 'text';
            const optionsText = optionsToText(field);
            const row = document.createElement('div');
            row.setAttribute('data-field-row', '');
            row.className = 'space-y-3 border border-slate-200 rounded-xl p-3';

            row.innerHTML =
                '<div class="grid grid-cols-1 md:grid-cols-12 gap-3">' +
                    '<div class="md:col-span-2">' +
                        '<label class="block text-xs font-medium text-slate-700">' + @json(__('admin.field_key')) + '</label>' +
                        '<input type="text" data-input-key value="' + escAttr(field.key ?? '') + '" required ' +
                        'pattern="[a-z0-9_]+" title=' + @json(__('admin.field_key_pattern_hint')) + ' ' +
                        'class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">' +
                    '</div>' +
                    '<div class="md:col-span-3">' +
                        '<label class="block text-xs font-medium text-slate-700">' + @json(__('admin.field_label_ar')) + '</label>' +
                        '<input type="text" data-input-label-ar value="' + escAttr((field.label && field.label.ar) ? field.label.ar : '') + '" required ' +
                        'class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">' +
                    '</div>' +
                    '<div class="md:col-span-3">' +
                        '<label class="block text-xs font-medium text-slate-700">' + @json(__('admin.field_label_en')) + '</label>' +
                        '<input type="text" data-input-label-en value="' + escAttr((field.label && field.label.en) ? field.label.en : '') + '" required ' +
                        'class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">' +
                    '</div>' +
                    '<div class="md:col-span-3">' +
                        '<label class="block text-xs font-medium text-slate-700">' + @json(__('admin.field_type')) + '</label>' +
                        '<select data-input-type required ' +
                        'class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">' +
                        createTypeOptions(type) +
                        '</select>' +
                    '</div>' +
                    '<div class="md:col-span-1 flex items-end">' +
                        '<button type="button" data-remove-button ' +
                        'class="w-full inline-flex items-center justify-center px-3 py-2 rounded-xl border border-rose-200 bg-rose-50 hover:bg-rose-100 text-rose-700 text-sm font-semibold">&times;</button>' +
                    '</div>' +
                '</div>' +
                '<div data-options-panel class="hidden">' +
                    '<label class="block text-xs font-medium text-slate-700">' + @json(__('admin.field_dropdown_options')) + '</label>' +
                    '<textarea data-input-options-text rows="3" ' +
                    'class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" ' +
                    'placeholder=' + @json(__('admin.field_dropdown_options_hint')) + '></textarea>' +
                '</div>';

            row.querySelector('[data-remove-button]').addEventListener('click', function () {
                row.remove();
                reindexFieldNames();
                updateEmptyState();
            });

            const typeSelect = row.querySelector('[data-input-type]');
            typeSelect.addEventListener('change', function () {
                toggleOptionsRow(row);
            });

            fieldsContainer.appendChild(row);
            const optionsTextarea = row.querySelector('[data-input-options-text]');
            if (optionsTextarea) {
                optionsTextarea.value = optionsText;
            }
            reindexFieldNames();
            toggleOptionsRow(row);
            updateEmptyState();
        };

        addButton.addEventListener('click', function () {
            addField({
                key: '',
                label: { ar: '', en: '' },
                type: 'text',
            });
        });

        if (Array.isArray(oldFields) && oldFields.length > 0) {
            oldFields.forEach(function (field) {
                addField({
                    key: field && field.key ? field.key : '',
                    label: {
                        ar: field && field.label && field.label.ar ? field.label.ar : '',
                        en: field && field.label && field.label.en ? field.label.en : '',
                    },
                    type: field && field.type ? field.type : 'text',
                    options: field && field.options ? field.options : undefined,
                });
            });
        } else {
            updateEmptyState();
        }
    });
</script>
