<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('recurring-debts:generate')->dailyAt('06:00');
