<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SyncRolePermissionsRequest;
use App\Services\Admin\PermissionService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Spatie\Permission\Models\Role;

class RolePermissionController extends Controller
{
    public function __construct(protected PermissionService $permissionService) {}

    public function edit(Role $role): View
    {
        $permissions = $this->permissionService->listPermissionsForGuard('web');
        $assignedPermissions = $this->permissionService->getAssignedPermissionIds($role);

        return view('admin.role-permissions.edit', compact('role', 'permissions', 'assignedPermissions'));
    }

    public function update(SyncRolePermissionsRequest $request, Role $role): RedirectResponse
    {
        $this->permissionService->syncRolePermissions($role, $request->validated(), 'web');

        return redirect()
            ->route('admin.roles.index')
            ->with('success', __('admin.role_permissions_updated'));
    }
}
