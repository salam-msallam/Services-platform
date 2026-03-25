<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web\Admin;

use App\Enums\StatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AcceptBusinessAccountRequest;
use App\Http\Requests\Admin\RejectBusinessAccountRequest;
use App\Models\BusinessAccount;
use App\Services\Admin\BusinessAccountReviewService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class BusinessAccountReviewController extends Controller
{
    public function __construct(protected BusinessAccountReviewService $reviewService) {}

    public function index(): View
    {
        $businessAccounts = $this->reviewService->listPending();

        return view('admin.business-accounts.index', compact('businessAccounts'));
    }

    public function accept(
        AcceptBusinessAccountRequest $request,
        BusinessAccount $businessAccount,
    ): RedirectResponse {
        $updated = $this->reviewService->accept($businessAccount);

        if ($updated->status === StatusEnum::Accepted) {
            return redirect()
                ->back()
                ->with('success', __('admin.business_account_accepted'));
        }

        return redirect()
            ->back()
            ->withErrors([
                'business_account' => __('admin.business_account_not_pending'),
            ]);
    }

    public function reject(
        RejectBusinessAccountRequest $request,
        BusinessAccount $businessAccount,
    ): RedirectResponse {
        $updated = $this->reviewService->reject($businessAccount);

        if ($updated->status === StatusEnum::Rejected) {
            return redirect()
                ->back()
                ->with('success', __('admin.business_account_rejected'));
        }

        return redirect()
            ->back()
            ->withErrors([
                'business_account' => __('admin.business_account_not_pending'),
            ]);
    }
}

