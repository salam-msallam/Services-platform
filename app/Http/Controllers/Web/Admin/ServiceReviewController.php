<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web\Admin;

use App\Enums\StatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AcceptServiceRequest;
use App\Http\Requests\Admin\RejectServiceRequest;
use App\Models\Service;
use App\Services\Admin\ServiceReviewService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ServiceReviewController extends Controller
{
    public function __construct(protected ServiceReviewService $reviewService) {}

    public function index(Request $request): View
    {
        $status = $request->query('status');
        $tab = $request->string('tab')->toString();
        $tab = in_array($tab, ['active', 'trashed'], true) ? $tab : 'active';

        $services = $this->reviewService->listAll(
            is_string($status) ? $status : null,
            $tab
        );

        return view('admin.services.index', compact('services', 'status', 'tab'));
    }

    public function show(Service $service): View
    {
        $service = $this->reviewService->findForReview($service);

        return view('admin.services.show', compact('service'));
    }

    public function accept(
        AcceptServiceRequest $request,
        Service $service,
    ): RedirectResponse {
        $updated = $this->reviewService->accept($service);
        $backToShow = $request->boolean('back_to_show');

        if ($updated->status === StatusEnum::Accepted) {
            if ($backToShow) {
                return redirect()
                    ->route('admin.services.show', $service)
                    ->with('success', __('admin.service_accepted'));
            }

            return redirect()
                ->back()
                ->with('success', __('admin.service_accepted'));
        }

        if ($backToShow) {
            return redirect()
                ->route('admin.services.show', $service)
                ->withErrors([
                    'service' => __('admin.service_not_pending'),
                ]);
        }

        return redirect()
            ->back()
            ->withErrors([
                'service' => __('admin.service_not_pending'),
            ]);
    }

    public function reject(
        RejectServiceRequest $request,
        Service $service,
    ): RedirectResponse {
        $updated = $this->reviewService->reject($service);
        $backToShow = $request->boolean('back_to_show');

        if ($updated->status === StatusEnum::Rejected) {
            if ($backToShow) {
                return redirect()
                    ->route('admin.services.show', $service)
                    ->with('success', __('admin.service_rejected'));
            }

            return redirect()
                ->back()
                ->with('success', __('admin.service_rejected'));
        }

        if ($backToShow) {
            return redirect()
                ->route('admin.services.show', $service)
                ->withErrors([
                    'service' => __('admin.service_not_pending'),
                ]);
        }

        return redirect()
            ->back()
            ->withErrors([
                'service' => __('admin.service_not_pending'),
            ]);
    }
}

