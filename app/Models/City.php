<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class City extends Model
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
        'x',
        'y',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'x' => 'decimal:7',
            'y' => 'decimal:7',
        ];
    }

    public function services(): HasMany
    {
        return $this->hasMany(Service::class);
    }
}
