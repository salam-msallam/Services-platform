<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class Category extends Model
{
    use HasFactory;
    use HasTranslations;

    /**
     * @var array<int, string>
     */
    public array $translatable = [
        'name',
    ];

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'dynamic_fields',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'dynamic_fields' => 'array',
        ];
    }

    public function subCategories(): HasMany
    {
        return $this->hasMany(SubCategory::class);
    }

    public function services(): HasMany
    {
        return $this->hasMany(Service::class);
    }
}
