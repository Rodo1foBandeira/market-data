<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Trades_WING19;

class MiningMarketData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'miningmarketdata:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mining Market Data';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $test = new Trades_WING19;
        $test->unix_timestamp_open = time();
        $test->unix_timestamp_close = time();
        $test->save();
    }
}
