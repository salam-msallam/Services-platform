<?php

declare(strict_types=1);

namespace App\Services\Report;

use App\Enums\StatusEnum;
use App\Exceptions\Report\NotAllowedToReportException;
use App\Exceptions\Report\OneReportYouDoToSameOrderException;
use App\Models\Order;
use App\Models\Report;
use App\Models\Service;
use App\Models\User;
use App\Services\Notification\NotificationService;
use DomainException;
use Illuminate\Support\Facades\DB;

class ReportService
{
    public function __construct(
        private readonly NotificationService $notificationService,
    ) {}

    public function store(User $user, array $data): Report
    {
        $order = Order::findOrFail($data['order_id']);
        $orderUser = $order->businessAccount->user_id;

        $alreadyReported = $user->reports()
        ->where('order_id', $data['order_id'])
        ->exists();
        if ($alreadyReported) {
            throw new OneReportYouDoToSameOrderException(__('api.report_one_you_can_not'));
        }

        // 1. تحقق من الصلاحية (هل هذا الشخص طرف في الطلب؟)
        if ($orderUser != $user->id) {
             throw new NotAllowedToReportException(__('api.report_not_allowed'));
        }

        // 3. التنفيذ
        $report = $user->reports()->create([
            'order_id' => $data['order_id'],
            'reason' => $data['reason']
        ]);

        $this->notificationService->notifyNewReport($report->id);

        return $report;
    }

}
