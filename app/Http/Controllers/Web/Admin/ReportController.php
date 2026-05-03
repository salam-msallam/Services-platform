<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web\Admin;

use App\Enums\StatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ResolveReportRequest;
use App\Models\Report;
use App\Services\Admin\ReportService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class ReportController extends Controller
{
    public function __construct(protected ReportService $reportService) {}

    public function index(): View
    {
        $reports = $this->reportService->listAll();

        return view('admin.reports.index', compact('reports'));
    }

    public function show(Report $report): View
    {
        $report = $this->reportService->findForReview($report);

        return view('admin.reports.show', compact('report'));
    }

    public function resolve(
        ResolveReportRequest $request,
        Report $report,
    ): RedirectResponse {
        $updated = $this->reportService->resolve($report);
        $backToShow = $request->boolean('back_to_show');

        if ($updated->status === StatusEnum::Resolved) {
            if ($backToShow) {
                return redirect()
                    ->route('admin.reports.show', $report)
                    ->with('success', __('api.report_resolved'));
            }

            return redirect()
                ->back()
                ->with('success', __('api.report_resolved'));
        }

        if ($backToShow) {
            return redirect()
                ->route('admin.reports.show', $report)
                ->withErrors([
                    'report' => __('api.report_not_pending'),
                ]);
        }

        return redirect()
            ->back()
            ->withErrors([
                'report' => __('api.report_not_pending'),
            ]);
    }
}
