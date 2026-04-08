<?php

declare(strict_types=1);

namespace App\Services\Evaluation;

use App\Enums\StatusEnum;
use App\Models\Evaluation;
use App\Models\Order;
use App\Models\User;
use DomainException;
use Illuminate\Support\Facades\DB;

class EvaluationService
{
    public const ERROR_NOT_ALLOWED_TO_RATE = 'api.evaluation_rate_not_allowed';

    public const ERROR_ALREADY_RATED = 'api.evaluation_already_rated';

    public function store(User $user, array $data): Evaluation
    {
        $serviceId = (int) $data['service_id'];

        $hasAcceptedOrder = Order::query()
            ->where('service_id', $serviceId)
            ->where('status', StatusEnum::Accepted->value)
            ->whereHas('businessAccount', function ($query) use ($user): void {
                $query->where('user_id', $user->id);
            })
            ->exists();

        if (! $hasAcceptedOrder) {
            throw new DomainException(self::ERROR_NOT_ALLOWED_TO_RATE);
        }

        $alreadyRated = Evaluation::query()
            ->where('user_id', $user->id)
            ->where('service_id', $serviceId)
            ->exists();

        if ($alreadyRated) {
            throw new DomainException(self::ERROR_ALREADY_RATED);
        }

        return DB::transaction(function () use ($user, $data, $serviceId): Evaluation {
            return $user->evaluations()->create([
                'service_id' => $serviceId,
                'rating' => (int) $data['rating'],
                'comment' => $data['comment'] ?? null,
            ]);
        });
    }
}
