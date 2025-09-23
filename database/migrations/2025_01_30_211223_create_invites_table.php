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
        Schema::create('invites', function (Blueprint $table): void {
            $table->uuid('uuid')->primary();
            $table->foreignId('group_id')->constrained()->cascadeOnDelete();
            $table->string('name')->nullable();
            $table->boolean('is_reusable')->default(false);
            $table->unsignedTinyInteger('duration_days')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invites');
    }
};
