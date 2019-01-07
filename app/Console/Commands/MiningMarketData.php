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
    	$nextMin = floatval(gmdate("i.s", time())) + 1;
    	while(floatval(gmdate("i.s", time())) <= $nextMin) {
    		$res = $client->request('GET', 'https://mdgateway01.easynvest.com.br/iwg/snapshot/?t=webgateway&c=5448062&q=WING19|WDOG19|PETR4|VALE3|ITSA4');
    		$retorno = json_decode($res->getBody(), false);
    		//if($retorno->Value[0]->Ps->STSD != 'open')
    		//	break;
			$this->miningData($this->filterData($retorno));
		}				
    }

    private function filterData($data){
        foreach($data->Value as $key => $item){
            $ticker = $item->S;
            //if($item->Ps->STSD == 'open')
                foreach($item->Ts as $k2 => $ts)
                    if ($this->listTimeTradeExist(new Timetrade($ts->Br, $ts->Sr, $ts->Q, $ts->P, $ts->DT)))
                        break;
            array_splice($data->Value[$key]->Ts, $k2);
        }
        return $data;
    }

    private function miningData($data){
    	foreach($data->Value as $key => $item){
    		$ticker = $item->S;
    		//if($item->Ps->STSD == 'open')
	    		foreach($item->Ts as $k2 => $ts){
	    			$temp_times_trades = new TempTimeTrade;
                    $date = new DateTime($ts->DT, new DateTimeZone('America/Sao_Paulo'));
			    	$temp_times_trades->unix_timestamp = $date->format('U');
			    	$temp_times_trades->ticker = $ticker;
			    	/*$temp_times_trades->bank_code_purchase = $ts->Br;
			    	$temp_times_trades->bank_code_sale = $ts->Sr;
			    	$temp_times_trades->price = $ts->P;
			    	$temp_times_trades->qtd = $ts->Q;
			    	$temp_times_trades->qtd_buss = $item->Ps->TC; // Business
			    	$temp_times_trades->qtd_tot = $item->Ps->TT; // Qtd Total Papers||Contracts
			    	if(count($item->BBP->Bd) > 0) {
			    		$temp_times_trades->bid_price = $item->BBP->Bd[0]->P;
			    		$temp_times_trades->bid_qtd = $item->BBP->Bd[0]->Q;
			    	}
			    	if(count($item->BBP->Ak) > 0) {
			    		$temp_times_trades->ask_price = $item->BBP->Ak[0]->P;
			    		$temp_times_trades->ask_qtd = $item->BBP->Ak[0]->P;
			    	}*/
			    	$temp_times_trades->save();
			    	array_push($this->listTimeTradeExist, new Timetrade($ts->Br, $ts->Sr, $ts->Q, $ts->P, $ts->DT));
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