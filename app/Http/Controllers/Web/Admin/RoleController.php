<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreRoleRequest;
use App\Http\Requests\Admin\UpdateRoleRequest;
use App\Services\Admin\RoleService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function __construct(protected RoleService $roleService) {}

    public function index(): View
    {
        $roles = $this->roleService->listRolesWithPermissionCount();

        return view('admin.roles.index', compact('roles'));
    }

    public function create(): View
    {
        return view('admin.roles.create');
    }

    public function store(StoreRoleRequest $request): RedirectResponse
    {
        $this->roleService->createRole($request->validated());

        return redirect()
            ->route('admin.roles.index')
            ->with('success', __('admin.role_created'));
    }

    public function edit(Role $role): View
    {
        return view('admin.roles.edit', compact('role'));
    }

    public function update(UpdateRoleRequest $request, Role $role): RedirectResponse
    {
        $this->roleService->updateRole($role, $request->validated());

        return redirect()
            ->route('admin.roles.index')
            ->with('success', __('admin.role_updated'));
    }

    public function destroy(Role $role): RedirectResponse
    {
        try {
            $this->roleService->deleteRole($role);
        } catch (AuthorizationException $e) {
            return back()->withErrors([
                'role' => $e->getMessage(),
            ]);
        }

        return redirect()
            ->route('admin.roles.index')
            ->with('success', __('admin.role_deleted'));
    }
}
