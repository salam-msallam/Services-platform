<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAdminRequest;
use App\Services\Admin\AdminService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class AdminController extends Controller
{
    public function __construct(
        private readonly AdminService $adminService,
    ) {}

    public function index(): View
    {
        $admins = $this->adminService->listAdmins();

        return view('admin.admins.index', compact('admins'));
    }

    public function create(): View
    {
        return view('admin.admins.create');
    }

    public function store(StoreAdminRequest $request): RedirectResponse
    {
        $this->adminService->createAdmin(
            $request->validated('name'),
            $request->validated('email'),
            $request->validated('password'),
        );

        return redirect()
            ->route('admin.admins.index')
            ->with('success', 'Administrator created successfully.');
    }
}
