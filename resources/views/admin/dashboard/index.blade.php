@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h4 class="card-title mb-3">Welcome, {{ auth()->user()->name }}</h4>

                    @if(auth()->user()->admin)
                        <p class="mb-1">Email: {{ auth()->user()->admin->email }}</p>
                        @if(auth()->user()->admin->main_admin)
                            <p class="text-success mb-0">You are the main administrator.</p>
                        @endif
                    @endif

                    @can('manage admins')
                        <hr>
                        <a href="{{ route('admin.admins.index') }}" class="btn btn-outline-primary btn-sm">
                            Manage Admins
                        </a>
                    @endcan
                </div>
            </div>
        </div>
    </div>
@endsection

