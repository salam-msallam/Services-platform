<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAdminRequest;
use App\Http\Requests\UpdateAdminRequest;
use App\Models\Admin;
use App\Services\Admin\AdminService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class AdminController extends Controller
{
    public function __construct(protected AdminService $adminService) {}

    public function index(): View
    {
        $admins = $this->adminService->listAdmins();

        return view('admin.admins.index', compact('admins'));
    }

    public function create(): View
    {
        $roles = Role::query()
            ->where('guard_name', 'web')
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('admin.admins.create', compact('roles'));
    }

    public function store(StoreAdminRequest $request): RedirectResponse
    {
        $this->adminService->createAdmin(
            $request->validated('name'),
            $request->validated('email'),
            $request->validated('password'),
            $request->validated('role_ids'),
        );

        return redirect()
            ->route('admin.admins.index')
            ->with('success', __('admin.admin_created'));
    }

    public function edit(Admin $admin): View
    {
        $admin->loadMissing('user.roles');

        $roles = Role::query()
            ->where('guard_name', 'web')
            ->orderBy('name')
            ->get(['id', 'name']);

        $assignedRoleIds = $admin->user
            ? $admin->user->roles->pluck('id')->all()
            : [];

        return view('admin.admins.edit', compact('admin', 'roles', 'assignedRoleIds'));
    }

    public function update(UpdateAdminRequest $request, Admin $admin): RedirectResponse
    {
        try {
            $this->adminService->updateAdmin($admin, $request->validated());
        } catch (AuthorizationException $e) {
            return back()->withErrors([
                'admin' => $e->getMessage(),
            ])->withInput();
        }

        return redirect()
            ->route('admin.admins.index')
            ->with('success', __('admin.admin_updated'));
    }

    public function destroy(Request $request, Admin $admin): RedirectResponse
    {
        try {
            $this->adminService->deleteAdmin($admin, (int) $request->user()->id);
        } catch (AuthorizationException $e) {
            return back()->withErrors([
                'admin' => $e->getMessage(),
            ]);
        }

        return redirect()
            ->route('admin.admins.index')
            ->with('success', __('admin.admin_deleted'));
    }
}
