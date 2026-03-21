@extends('layouts.admin')

@section('title', __('admin.set_permissions'))

@section('content')
    <div class="max-w-3xl mx-auto">
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6">
            <h1 class="text-xl font-semibold text-slate-900">
                {{ __('admin.set_permissions') }}: {{ $role->name }}
            </h1>

            @if($errors->any())
                <div class="mt-4 bg-rose-50 border border-rose-200 text-rose-700 rounded-2xl px-4 py-3 text-sm">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.roles.permissions.update', $role) }}" class="mt-5 space-y-5">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @forelse($permissions as $permission)
                        <label class="flex items-center gap-2 rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 cursor-pointer">
                            <input
                                type="checkbox"
                                name="permissions[]"
                                value="{{ $permission->id }}"
                                class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"
                                {{ in_array($permission->id, $assignedPermissions, true) ? 'checked' : '' }}
                            >
                            <span class="text-sm text-slate-800">{{ $permission->name }}</span>
                        </label>
                    @empty
                        <p class="text-sm text-slate-500">{{ __('admin.no_permissions') }}</p>
                    @endforelse
                </div>

                <div class="flex items-center justify-between gap-3">
                    <a href="{{ route('admin.roles.index') }}" class="inline-flex items-center px-4 py-2.5 rounded-xl border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 text-sm font-semibold">
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

