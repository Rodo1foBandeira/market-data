<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\TempTimeTrade;

use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;

class TimeTrade {
	public $bankCodePurchase;
	public $bankCodeSale;
	public $qtd;
	public $price;
	public $time;
	
	function __construct($bcp, $bcs, $q, $p, $t){
		$this->bankCodePurchase = $bcp;
		$this->bankCodeSale = $bcs;
		$this->qtd = $q;
		$this->price = $p;
		$this->time = $t; 	
	}
}

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
    
    private $listTimeTrades = array();
    private $i;

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {    	
    	$client = new Client();
    	for($this->i=0;$this->i < 60; $this->i++) {
    		$res = $client->request('GET', 'https://mdgateway01.easynvest.com.br/iwg/snapshot/?t=webgateway&c=5448062&q=WING19|WDOF19|CMIG4|PETR4|USIM5|CSNA3');//
			$this->miningData(json_decode($res->getBody(), false));
			sleep(1);
		}				
    }
    
    private function miningData($data){
    	foreach($data->Value as $key => $item){
    		$ticker = $item->S;
    		foreach($item->Ts as $k2 => $ts){
    			//if ($this->listTimeTradeExist(new Timetrade($ts->Br, $ts->Sr, $ts->Q, $ts->P, strtotime($ts->DT))))
    				//continue;
    			//array_push($this->listTimeTrades, new Timetrade($ts->Br, $ts->Sr, $ts->Q, $ts->P, strtotime($ts->DT)));
    			if(TempTimeTrade::where([
    					['ticker', '=', $ticker],
    					['unix_timestamp', '=', strtotime($ts->DT)],
    					['bank_code_purchase', '=', $ts->Br],
    					['bank_code_sale', '=', $ts->Sr],
    					['price', '=', $ts->P],
    					['volume', '=', $ts->Q]
    				])->exists())
    				continue;
    			$temp_times_trades = new TempTimeTrade;
		    	$temp_times_trades->unix_timestamp = strtotime($ts->DT);
		    	$temp_times_trades->ticker = $ticker;
		    	$temp_times_trades->bank_code_purchase = $ts->Br;
		    	$temp_times_trades->bank_code_sale = $ts->Sr;
		    	$temp_times_trades->price = $ts->P;
		    	$temp_times_trades->volume = $ts->Q;
		    	if(count($item->Bk->Bd) > 0) {
		    		$temp_times_trades->bid_price = $item->Bk->Bd[0]->P;
		    		$temp_times_trades->bid_qtd = $item->Bk->Bd[0]->Q;
		    	}
		    	if(count($item->Bk->Ak) > 0) {
		    		$temp_times_trades->ask_price = $item->Bk->Ak[0]->P;
		    		$temp_times_trades->ask_qtd = $item->Bk->Ak[0]->P;
		    	}
		    	$temp_times_trades->save();
    		}    		
    	}    	
    }
    
    private function listTimeTradeExist($tt){
    	foreach($this->listTimeTrades as $key => $item){
    		if (($item->bankCodePurchase == $tt->bankCodePurchase) && ($item->bankCodeSale == $tt->bankCodeSale) && ($item->qtd == $tt->qtd) && ($item->price == $tt->price) && ($item->time == $tt->time))
    			return true;
    	}
    }
}