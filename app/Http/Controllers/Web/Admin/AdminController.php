<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAdminRequest;
use App\Models\Admin;
use App\Services\Admin\AdminService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

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
            ->with('success', __('admin.admin_created'));
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
