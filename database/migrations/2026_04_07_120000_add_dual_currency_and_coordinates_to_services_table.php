<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('services', function (Blueprint $table): void {
            $table->unsignedBigInteger('price_syp')->after('price');
            $table->decimal('price_usd', 12, 2)->after('price_syp');
            $table->decimal('latitude', 10, 8)->after('dynamic_values');
            $table->decimal('longitude', 11, 8)->after('latitude');
        });
    }

    public function down(): void
    {
        Schema::table('services', function (Blueprint $table): void {
            $table->dropColumn(['price_syp', 'price_usd', 'latitude', 'longitude']);
        });
    }
};
