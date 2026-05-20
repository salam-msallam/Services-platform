<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        Schema::table('services', function (Blueprint $table): void {
            $table->dropForeign(['category_id']);
            $table->dropForeign(['city_id']);
        });

        Schema::table('services', function (Blueprint $table): void {
            $table->foreignId('city_id')->nullable()->change();
        });

        Schema::table('services', function (Blueprint $table): void {
            $table->foreign('category_id')->references('id')->on('categories')->restrictOnDelete();
            $table->foreign('city_id')->references('id')->on('cities')->nullOnDelete();
        });
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        Schema::table('services', function (Blueprint $table): void {
            $table->dropForeign(['category_id']);
            $table->dropForeign(['city_id']);
        });

        Schema::table('services', function (Blueprint $table): void {
            $table->foreignId('city_id')->nullable(false)->change();
        });

        Schema::table('services', function (Blueprint $table): void {
            $table->foreign('category_id')->references('id')->on('categories')->cascadeOnDelete();
            $table->foreign('city_id')->references('id')->on('cities')->cascadeOnDelete();
        });
    }
};
