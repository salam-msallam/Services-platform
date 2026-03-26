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
use Illuminate\Http\Request;

class BusinessAccountReviewController extends Controller
{
    public function __construct(protected BusinessAccountReviewService $reviewService) {}

    public function index(Request $request): View
    {
        $status = $request->query('status');
        $businessAccounts = $this->reviewService->listAll(
            is_string($status) ? $status : null
        );

        return view('admin.business-accounts.index', compact('businessAccounts', 'status'));
    }

    public function show(BusinessAccount $businessAccount): View
    {
        $businessAccount = $this->reviewService->findForReview($businessAccount);

        return view('admin.business-accounts.show', compact('businessAccount'));
    }

    public function accept(
        AcceptBusinessAccountRequest $request,
        BusinessAccount $businessAccount,
    ): RedirectResponse {
        $updated = $this->reviewService->accept($businessAccount);
        $backToShow = $request->boolean('back_to_show');

        if ($updated->status === StatusEnum::Accepted) {
            if ($backToShow) {
                return redirect()
                    ->route('admin.business-accounts.show', $businessAccount)
                    ->with('success', __('admin.business_account_accepted'));
            }

            return redirect()
                ->back()
                ->with('success', __('admin.business_account_accepted'));
        }

        if ($backToShow) {
            return redirect()
                ->route('admin.business-accounts.show', $businessAccount)
                ->withErrors([
                    'business_account' => __('admin.business_account_not_pending'),
                ]);
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
        $backToShow = $request->boolean('back_to_show');

        if ($updated->status === StatusEnum::Rejected) {
            if ($backToShow) {
                return redirect()
                    ->route('admin.business-accounts.show', $businessAccount)
                    ->with('success', __('admin.business_account_rejected'));
            }

            return redirect()
                ->back()
                ->with('success', __('admin.business_account_rejected'));
        }

        if ($backToShow) {
            return redirect()
                ->route('admin.business-accounts.show', $businessAccount)
                ->withErrors([
                    'business_account' => __('admin.business_account_not_pending'),
                ]);
        }

        return redirect()
            ->back()
            ->withErrors([
                'business_account' => __('admin.business_account_not_pending'),
            ]);
    }
}

