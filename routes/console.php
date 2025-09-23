<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schedule;

Schedule::command('recurring-debts:generate')->dailyAt('06:00');
