<?php

declare(strict_types=1);

namespace App\Services\Admin;

use App\Enums\StatusEnum;
use App\Models\Report;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class ReportService
{
    public function listAll(): LengthAwarePaginator
    {
        return Report::query()
            ->with([
                'user',
                'order.service',
                'order.businessAccount.user',
            ])
            ->latest()
            ->paginate(10)
            ->withQueryString();
    }

    public function findForReview(Report $report): Report
    {
        return $report->load([
            'user',
            'order.service',
            'order.businessAccount.user',
        ]);
    }

    public function resolve(Report $report): Report
    {
        return DB::transaction(function () use ($report): Report {
            $report->refresh();

            if ($report->status === StatusEnum::Resolved) {
                return $report->loadMissing([
                    'user',
                    'order.service',
                    'order.businessAccount.user',
                ]);
            }

            $report->update([
                'status' => StatusEnum::Resolved,
            ]);

            return $report->fresh([
                'user',
                'order.service',
                'order.businessAccount.user',
            ]);
        });
    }
}
