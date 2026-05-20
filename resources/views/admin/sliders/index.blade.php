@extends('layouts.admin')

@section('title', __('admin.manage_sliders'))

@section('content')
    <div class="space-y-6">
        <div>
            <h1 class="text-2xl font-semibold text-slate-900">{{ __('admin.sliders') }}</h1>
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

        @can('create-sliders')
            <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6">
                <h2 class="text-lg font-semibold text-slate-900">{{ __('admin.create_slider') }}</h2>

                <form method="POST" action="{{ route('admin.sliders.store') }}" enctype="multipart/form-data" class="mt-5 space-y-5">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="title_ar" class="block text-sm font-medium text-slate-700">{{ __('admin.slider_title_ar') }}</label>
                            <input
                                type="text"
                                name="title[ar]"
                                id="title_ar"
                                value="{{ old('title.ar') }}"
                                required
                                class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                            >
                        </div>

                        <div>
                            <label for="title_en" class="block text-sm font-medium text-slate-700">{{ __('admin.slider_title_en') }}</label>
                            <input
                                type="text"
                                name="title[en]"
                                id="title_en"
                                value="{{ old('title.en') }}"
                                required
                                class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                            >
                        </div>
                    </div>

                    <div>
                        <label for="image" class="block text-sm font-medium text-slate-700">{{ __('admin.slider_image') }}</label>
                        <input
                            type="file"
                            name="image"
                            id="image"
                            accept="image/jpeg,image/png,image/webp"
                            required
                            class="mt-1 block w-full text-sm text-slate-700 file:mr-4 file:rounded-xl file:border-0 file:bg-indigo-50 file:px-4 file:py-2.5 file:text-sm file:font-semibold file:text-indigo-700 hover:file:bg-indigo-100"
                        >
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="inline-flex items-center px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold">
                            {{ __('admin.save') }}
                        </button>
                    </div>
                </form>
            </div>
        @endcan

        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
            <div class="p-4 overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider text-xs">
                    <tr>
                        <th class="px-4 py-3 {{ app()->getLocale() === 'ar' ? 'text-right' : 'text-left' }}">{{ __('admin.image') }}</th>
                        <th class="px-4 py-3 {{ app()->getLocale() === 'ar' ? 'text-right' : 'text-left' }}">{{ __('admin.slider_title_ar') }}</th>
                        <th class="px-4 py-3 {{ app()->getLocale() === 'ar' ? 'text-right' : 'text-left' }}">{{ __('admin.slider_title_en') }}</th>
                        <th class="px-4 py-3 {{ app()->getLocale() === 'ar' ? 'text-right' : 'text-left' }}">{{ __('admin.status') }}</th>
                        <th class="px-4 py-3 {{ app()->getLocale() === 'ar' ? 'text-right' : 'text-left' }}">{{ __('admin.display') }}</th>
                        <th class="px-4 py-3 {{ app()->getLocale() === 'ar' ? 'text-right' : 'text-left' }}">{{ __('admin.created_at') }}</th>
                        <th class="px-4 py-3 {{ app()->getLocale() === 'ar' ? 'text-right' : 'text-left' }}">{{ __('admin.actions') }}</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                    @forelse($sliders as $slider)
                        <tr class="hover:bg-slate-50/80 transition align-top">
                            <td class="px-4 py-3">
                                @php($imageUrl = $slider->getFirstMediaUrl('scroll_bar_image'))
                                @if($imageUrl !== '')
                                    <img src="{{ $imageUrl }}" alt="{{ $slider->getTranslation('title', app()->getLocale(), false) }}" class="h-16 w-28 rounded-xl object-cover border border-slate-200">
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 font-medium text-slate-900">{{ $slider->getTranslation('title', 'ar', false) }}</td>
                            <td class="px-4 py-3 font-medium text-slate-900">{{ $slider->getTranslation('title', 'en', false) }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $slider->status ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">
                                    {{ $slider->status ? __('admin.active') : __('admin.inactive') }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                @if($currentSlider !== null && $slider->is($currentSlider))
                                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold bg-indigo-50 text-indigo-700 border border-indigo-100">
                                        {{ __('admin.currently_displayed') }}
                                    </span>
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-slate-700">{{ $slider->created_at?->format('Y-m-d H:i') }}</td>
                            <td class="px-4 py-3">
                                <div class="flex flex-wrap gap-2">
                                    @can('edit-sliders')
                                        <a href="#edit-slider-{{ $slider->id }}" class="inline-flex items-center px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-semibold">
                                            {{ __('admin.edit') }}
                                        </a>
                                    @endcan

                                    @can('delete-sliders')
                                        <form method="POST" action="{{ route('admin.sliders.destroy', $slider) }}" onsubmit="return confirm('{{ __('admin.confirm_delete') }}')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex items-center px-3 py-1.5 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-semibold">
                                                {{ __('admin.delete') }}
                                            </button>
                                        </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @can('edit-sliders')
                            <tr id="edit-slider-{{ $slider->id }}" class="bg-slate-50/70">
                                <td colspan="7" class="px-4 py-4">
                                    <form method="POST" action="{{ route('admin.sliders.update', $slider) }}" enctype="multipart/form-data" class="rounded-2xl border border-slate-200 bg-white p-4">
                                        @csrf
                                        @method('PUT')

                                        <div class="flex flex-col lg:flex-row lg:items-end gap-4">
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 flex-1">
                                                <div>
                                                    <label for="edit_title_ar_{{ $slider->id }}" class="block text-xs font-semibold text-slate-600">{{ __('admin.slider_title_ar') }}</label>
                                                    <input
                                                        type="text"
                                                        name="title[ar]"
                                                        id="edit_title_ar_{{ $slider->id }}"
                                                        value="{{ old('title.ar', $slider->getTranslation('title', 'ar', false)) }}"
                                                        required
                                                        class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                                    >
                                                </div>

                                                <div>
                                                    <label for="edit_title_en_{{ $slider->id }}" class="block text-xs font-semibold text-slate-600">{{ __('admin.slider_title_en') }}</label>
                                                    <input
                                                        type="text"
                                                        name="title[en]"
                                                        id="edit_title_en_{{ $slider->id }}"
                                                        value="{{ old('title.en', $slider->getTranslation('title', 'en', false)) }}"
                                                        required
                                                        class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                                    >
                                                </div>
                                            </div>

                                            <div class="w-full lg:w-40">
                                                <label for="edit_status_{{ $slider->id }}" class="block text-xs font-semibold text-slate-600">{{ __('admin.status') }}</label>
                                                @php($selectedStatus = (string) old('status', $slider->status ? '1' : '0'))
                                                <select
                                                    name="status"
                                                    id="edit_status_{{ $slider->id }}"
                                                    class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                                >
                                                    <option value="1" @selected($selectedStatus === '1')>{{ __('admin.active') }}</option>
                                                    <option value="0" @selected($selectedStatus === '0')>{{ __('admin.inactive') }}</option>
                                                </select>
                                            </div>

                                            <div class="w-full lg:w-64">
                                                <label for="edit_image_{{ $slider->id }}" class="block text-xs font-semibold text-slate-600">{{ __('admin.replace_slider_image') }}</label>
                                                <input
                                                    type="file"
                                                    name="image"
                                                    id="edit_image_{{ $slider->id }}"
                                                    accept="image/jpeg,image/png,image/webp"
                                                    class="mt-1 block w-full text-xs text-slate-700 file:mr-3 file:rounded-lg file:border-0 file:bg-indigo-50 file:px-3 file:py-2 file:text-xs file:font-semibold file:text-indigo-700 hover:file:bg-indigo-100"
                                                >
                                            </div>

                                            <button type="submit" class="inline-flex items-center justify-center px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold">
                                                {{ __('admin.update') }}
                                            </button>
                                        </div>
                                    </form>
                                </td>
                            </tr>
                        @endcan
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-12 text-center text-slate-500">
                                {{ __('admin.no_sliders') }}
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
