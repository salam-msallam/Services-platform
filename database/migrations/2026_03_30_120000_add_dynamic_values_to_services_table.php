<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('services', 'dynamic_values')) {
            return;
        }

        Schema::table('services', function (Blueprint $table): void {
            $table->json('dynamic_values')->nullable()->after('property_type');
        });
    }

    public function down(): void
    {
        if (! Schema::hasColumn('services', 'dynamic_values')) {
            return;
        }

        Schema::table('services', function (Blueprint $table): void {
            $table->dropColumn('dynamic_values');
        });
    }
};
