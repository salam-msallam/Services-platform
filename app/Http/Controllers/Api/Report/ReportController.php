<?php

namespace App\Http\Controllers\Api\Report;

use App\Http\Controllers\Controller;
use App\Http\Requests\Report\StoreReportRequest;
use App\Http\Resources\Report\ReportResource;
use App\Http\Responses\ApiResponse;
use App\Models\User;
use App\Services\Report\ReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ReportController extends Controller
{
    public function __construct(protected ReportService $reportService) {}

    public function store(StoreReportRequest $request ){
        $user = $request->user();
        Log::info($request->all());

        if (! $user instanceof User) {
            return ApiResponse::error(__('auth.unauthenticated'), [], 401);
        }
        $report=$this->reportService->store($user,$request->validated());
        return ApiResponse::success(
            ReportResource::make($report)->toArray($request),
            __('api.report_created'),
        );
    }
}
