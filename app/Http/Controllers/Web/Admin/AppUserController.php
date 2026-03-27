<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAppUserRequest;
use App\Http\Requests\UpdateAppUserRequest;
use App\Models\AppUser;
use App\Services\Admin\AppUserManagementService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class AppUserController extends Controller
{
    public function __construct(protected AppUserManagementService $appUserManagementService) {}

    public function index(): View
    {
        $appUsers = $this->appUserManagementService->listAppUsers();

        return view('admin.app-users.index', compact('appUsers'));
    }

    public function create(): View
    {
        return view('admin.app-users.create');
    }

    public function store(StoreAppUserRequest $request): RedirectResponse
    {
        $this->appUserManagementService->createAppUser(
            $request->validated('name'),
            $request->validated('phone'),
            $request->validated('password'),
        );

        return redirect()
            ->route('admin.app-users.index')
            ->with('success', __('admin.app_user_created'));
    }

    public function edit(AppUser $appUser): View
    {
        $appUser->loadMissing('user');

        return view('admin.app-users.edit', compact('appUser'));
    }

    public function update(UpdateAppUserRequest $request, AppUser $appUser): RedirectResponse
    {
        $this->appUserManagementService->updateAppUser($appUser, $request->validated());

        return redirect()
            ->route('admin.app-users.index')
            ->with('success', __('admin.app_user_updated'));
    }

    public function destroy(AppUser $appUser): RedirectResponse
    {
        $this->appUserManagementService->softDeleteAppUser($appUser);

        return redirect()
            ->route('admin.app-users.index')
            ->with('success', __('admin.app_user_deleted'));
    }

    public function restore(int $appUserId): RedirectResponse
    {
        $this->appUserManagementService->restoreAppUser($appUserId);

        return redirect()
            ->route('admin.app-users.index')
            ->with('success', __('admin.app_user_restored'));
    }
}
