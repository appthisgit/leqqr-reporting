<?php

namespace App\Console\Commands;

use App\Models\Receipt;
use Illuminate\Console\Command;

class DeleteOld extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'receipt:delete-old';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete old receipts';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Receipt::whereDate('created_at', '<=', now()->subDay(14))->delete();
    }
}
