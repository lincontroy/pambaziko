<?php

namespace App\Console\Commands;

use App\Models\Paybill;
use Illuminate\Console\Command;

class PaybillResetCommand extends Command
{
    protected $signature = 'paybill:reset';
    protected $description = 'Reset daily counters for all paybills';

    public function handle()
    {
        Paybill::query()->update([
            'current_count' => 0,
            'reset_at' => now(),
        ]);

        $this->info('Paybill counters reset successfully.');
    }
}