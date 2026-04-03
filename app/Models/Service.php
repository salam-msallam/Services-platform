<?php

namespace App\Models;

use App\Enums\StatusEnum;
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
        'price',
        'currency',
        'property_type',
        'dynamic_values',
        'status',
    ];

   
    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
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
