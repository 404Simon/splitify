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
        Schema::table('shared_debts', function (Blueprint $table): void {
            $table->foreignId('recurring_shared_debt_id')->nullable()->constrained()->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shared_debts', function (Blueprint $table): void {
            $table->dropForeign(['recurring_shared_debt_id']);
            $table->dropColumn('recurring_shared_debt_id');
        });
    }
};
