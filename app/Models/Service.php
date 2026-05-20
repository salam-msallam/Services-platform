<?php

namespace App\Models;

use App\Enums\StatusEnum;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Translatable\HasTranslations;

class Service extends Model implements HasMedia
{
    use HasFactory;
    use HasTranslations;
    use InteractsWithMedia;
    use SoftDeletes;

    public const PROPERTY_TYPE_SELLER = 'seller';

    public const PROPERTY_TYPE_RENT = 'rent';

    public const CURRENCY_USD = 'USD';

    public const CURRENCY_SYP = 'SYP';

    /**
     * @var list<string>
     */
    public const ALLOWED_CURRENCIES = [
        self::CURRENCY_USD,
        self::CURRENCY_SYP,
    ];

    protected static function booted(): void
    {
        static::deleting(function (Service $service): void {
            if ($service->isForceDeleting()) {
                return;
            }

            $service->orders()
                ->whereIn('status', [
                    StatusEnum::Pending->value,
                    StatusEnum::Rejected->value,
                ])
                ->whereNull('deleted_at')
                ->delete();
        });

        static::restoring(function (Service $service): void {
            $service->orders()
                ->onlyTrashed()
                ->whereIn('status', [
                    StatusEnum::Pending->value,
                    StatusEnum::Rejected->value,
                ])
                ->restore();
        });
    }

    public array $translatable = [
        'title',
        'description',
    ];


    protected $fillable = [
        'business_account_id',
        'category_id',
        'sub_category_id',
        'city_id',
        'title',
        'description',
        'quantity',
        'work_type',
        'price_syp',
        'price_usd',
        'currency',
        'latitude',
        'longitude',
        'property_type',
        'dynamic_values',
        'status',
    ];


    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'price_syp' => 'integer',
            'price_usd' => 'decimal:2',
            'latitude' => 'decimal:8',
            'longitude' => 'decimal:8',
            'quantity' => 'integer',
            'dynamic_values' => 'array',
            'status' => StatusEnum::class,
        ];
    }

    public function businessAccount(): BelongsTo
    {
        return $this->belongsTo(BusinessAccount::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function subCategory(): BelongsTo
    {
        return $this->belongsTo(SubCategory::class);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function evaluations(): HasMany
    {
        return $this->hasMany(Evaluation::class);
    }

    protected function averageRating(): Attribute
    {
        return Attribute::get(function (): ?float {
            if (array_key_exists('evaluations_avg_rating', $this->attributes)) {
                $raw = $this->attributes['evaluations_avg_rating'];

                return $raw === null ? null : round((float) $raw, 1);
            }

            $avg = $this->evaluations()->avg('rating');

            return $avg === null ? null : round((float) $avg, 1);
        });
    }

    protected function ratingsCount(): Attribute
    {
        return Attribute::get(function (): int {
            if (array_key_exists('evaluations_count', $this->attributes)) {
                return (int) $this->attributes['evaluations_count'];
            }

            return (int) $this->evaluations()->count();
        });
    }

    public function favoritedBy(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'favorites')
            ->withTimestamps();
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('images');
    }
}
