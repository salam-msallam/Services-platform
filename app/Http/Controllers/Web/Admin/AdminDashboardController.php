<?php

namespace App\Http\Controllers\Web\Admin;

use App\Enums\StatusEnum;
use App\Http\Controllers\Controller;
use App\Models\BusinessAccount;
use App\Models\Order;
use App\Models\Service;
use App\Services\Slider\SliderService;
use Illuminate\Contracts\View\View;

class AdminDashboardController extends Controller
{
    public function __construct(private readonly SliderService $sliderService) {}

    public function index(): View
    {
        $totalBusinessAccounts = BusinessAccount::count();
        $pendingReviewBusinessAccounts = BusinessAccount::query()
            ->where('status', StatusEnum::Pending->value)
            ->count();
        $pendingReviewServices = Service::query()
            ->where('status', StatusEnum::Pending->value)
            ->count();

        $pendingReviewsCount = $pendingReviewBusinessAccounts + $pendingReviewServices;
        $totalServices = Service::count();
        $totalOrders = Order::count();
        $pendingOrdersCount = Order::query()
            ->where('status', StatusEnum::Pending->value)
            ->count();
        $acceptedOrdersCount = Order::query()
            ->where('status', StatusEnum::Accepted->value)
            ->count();
        $rejectedOrdersCount = Order::query()
            ->where('status', StatusEnum::Rejected->value)
            ->count();
        $currentSlider = $this->sliderService->getDailyRotatingSlider();

        return view('admin.dashboard.index', compact(
            'totalBusinessAccounts',
            'pendingReviewsCount',
            'totalServices',
            'totalOrders',
            'pendingOrdersCount',
            'acceptedOrdersCount',
            'rejectedOrdersCount',
            'currentSlider',
        ));
    }
}
