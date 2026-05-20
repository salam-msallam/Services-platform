<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateAdminProfileRequest;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class AdminProfileController extends Controller
{
    public function edit(): View
    {
        /** @var User $user */
        $user = auth()->user();
        $user->loadMissing('admin');

        abort_if($user->admin === null, 403, __('admin.profile_missing'));

        return view('admin.profile.edit', compact('user'));
    }

    public function update(UpdateAdminProfileRequest $request): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();
        $user->loadMissing('admin');

        abort_if($user->admin === null, 403, __('admin.profile_missing'));

        DB::transaction(function () use ($request, $user): void {
            $user->update([
                'name' => $request->validated('name'),
            ]);

            $user->admin?->update([
                'email' => $request->validated('email'),
            ]);

            if ($request->filled('password')) {
                $user->update([
                    'password' => $request->validated('password'),
                ]);
            }
        });

        return redirect()
            ->route('admin.profile.edit')
            ->with('success', __('admin.profile_updated'));
    }
}
