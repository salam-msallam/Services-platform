<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table): void {
            $table->unsignedInteger('quantity')->default(1)->after('status');
            $table->date('date_of_need')->nullable()->after('quantity');
            $table->unsignedTinyInteger('time_of_need')->nullable()->after('date_of_need');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table): void {
            $table->dropColumn(['quantity', 'date_of_need', 'time_of_need']);
        });
    }
};
