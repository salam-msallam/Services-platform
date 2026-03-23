<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class ActivityType extends Model
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
    ];

    public function businessAccounts(): HasMany
    {
        return $this->hasMany(BusinessAccount::class);
    }
}
