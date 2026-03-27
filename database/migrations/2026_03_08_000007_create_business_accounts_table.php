<?php

use App\Enums\StatusEnum;
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
        Schema::create('business_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('city_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('x', 10, 7)->nullable();
            $table->decimal('y', 10, 7)->nullable();
            $table->foreignId('activity_type_id')->nullable()->constrained()->nullOnDelete();
            $table->json('name');
            $table->json('description')->nullable();
            $table->json('activities')->nullable();
            $table->string('license_number')->nullable();
            $table->string('status')->default(StatusEnum::Pending->value);
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['user_id', 'activity_type_id'], 'business_accounts_user_activity_type_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('business_accounts');
    }
};
