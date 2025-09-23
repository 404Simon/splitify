<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\RecurringSharedDebt;
use Exception;
use Illuminate\Console\Command;

final class GenerateRecurringSharedDebts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'recurring-debts:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate shared debts from active recurring shared debts that are due';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Checking for recurring shared debts to generate...');

        $recurringDebts = RecurringSharedDebt::where('is_active', true)
            ->where('next_generation_date', '<=', now())
            ->where(function ($query): void {
                $query->whereNull('end_date')
                    ->orWhere('end_date', '>=', now());
            })
            ->with(['users'])
            ->get();

        if ($recurringDebts->isEmpty()) {
            $this->info('No recurring shared debts to generate.');

            return Command::SUCCESS;
        }

        $generatedCount = 0;

        foreach ($recurringDebts as $recurringDebt) {
            if ($recurringDebt->shouldGenerate()) {
                try {
                    $sharedDebt = $recurringDebt->generateSharedDebt();
                    $generatedCount++;

                    $this->info("Generated shared debt '{$sharedDebt->name}' from recurring debt ID {$recurringDebt->id}");
                } catch (Exception $e) {
                    $this->error("Failed to generate shared debt from recurring debt ID {$recurringDebt->id}: {$e->getMessage()}");
                }
            }
        }

        $this->info("Successfully generated {$generatedCount} shared debts from recurring debts.");

        return Command::SUCCESS;
    }
}
