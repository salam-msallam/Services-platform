@extends('layouts.admin')

@section('title', __('admin.role_details'))

@section('content')
    <div class="space-y-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
            <div>
                <h1 class="text-2xl font-semibold text-slate-900">{{ __('admin.role_details') }}</h1>
                <p class="mt-1 text-sm text-slate-600">
                    {{ __('admin.role_name') }}: <span class="font-semibold text-slate-800">{{ $role->name }}</span>
                </p>
            </div>
            <a
                href="{{ route('admin.roles.index') }}"
                class="inline-flex items-center justify-center px-4 py-2.5 rounded-xl border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 font-semibold text-sm transition"
            >
                {{ __('admin.back_to_roles') }}
            </a>
        </div>

        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200">
                <h2 class="text-lg font-semibold text-slate-900">{{ __('admin.assigned_admins') }}</h2>
            </div>

            <div class="p-6">
                @if($assignedAdmins->isEmpty())
                    <p class="text-sm text-slate-500">{{ __('admin.no_assigned_admins') }}</p>
                @else
                    <div class="space-y-3">
                        @foreach($assignedAdmins as $assignedAdmin)
                            <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
                                <div class="text-sm font-semibold text-slate-900">{{ $assignedAdmin->name }}</div>
                                @if($assignedAdmin->admin?->email)
                                    <div class="text-xs text-slate-600 mt-1">{{ $assignedAdmin->admin->email }}</div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
