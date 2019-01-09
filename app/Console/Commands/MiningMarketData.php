<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\TempTimeTrade;

use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;

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
        $this->dateTime = now();
    }

    private $listTimeTrades = array();
    private $previousTimeTrades = array();
    private $dateTime;

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $t = new TempTimeTrade;
        $t->ticker = 'test';
        $t->unix_timestamp = time();
        $t->save();
        while (floatval(now()->format('H.i')) < 18.3){
            $client = new Client();
            $res = $client->request('GET', 'https://mdgateway01.easynvest.com.br/iwg/snapshot/?t=webgateway&c=5448062&q=WING19|WDOG19');
            if ($res->getStatusCode() == 200){
                $data = json_decode($res->getBody(), false);
                if($data->Value[0]->Ps->STSD != 'open' && floatval(now()->format('H.i')) > 9.05){
                    break;
                } else {
                    sleep(3);
                    continue;
                }
                $data = $this->filterData($data);
                $this->cleanPreviousData();
                $this->miningData($data);
            } else {
                sleep(3);
            }
            sleep(0.5);
        }
    }

    private function  cleanPreviousData(){
        if (intval(now()->format('i')) - intval($this->dateTime->format('i')) != 0){
            $this->dateTime = now();
            $this->previousTimeTrades = array();
        }
    }

    private function filterData($data){
        foreach($data->Value as $key => $item){
            $ticker = $item->S;
            foreach($item->Ts as $k2 => $ts){
                $date = new \DateTime($ts->DT); // , $this->dateTimeZone
                if ($this->previousTimeTradeExist($ticker, $ts->Br, $ts->Sr, $ts->Q, $ts->P, $date->format('U'))){
                    array_splice($data->Value[$key]->Ts, $k2);
                    break;
                }
            }
        }
        return $data;
    }

    private function previousTimeTradeExist($ticker, $bankCodePurchase, $bankCodeSale, $qtd, $price, $time){
        $result = false;
        foreach($this->previousTimeTrades as $key => $item){
            if (($item['ticker'] == $ticker) && ($item['bank_code_purchase'] == $bankCodePurchase) && ($item['bank_code_sale'] == $bankCodeSale) && ($item['qtd'] == $qtd) && ($item['price'] == $price) && ($item['unix_timestamp'] == $time)){
                $result = true;
                break;
            }
        }
        return $result;
    }

    private function miningData($data){
        $this->listTimeTrades = array();
    	foreach($data->Value as $key => $item){
    		$ticker = $item->S;
            foreach($item->Ts as $k2 => $ts){
                $date = new \DateTime($ts->DT); //, $this->dateTimeZone
                $timeTrade = [
                    'unix_timestamp' => $date->format('U'),
                    'ticker' => $ticker,
                    'bank_code_purchase' => $ts->Br,
                    'bank_code_sale' => $ts->Sr,
                    'price' => $ts->P,
                    'qtd' => $ts->Q,
                    'qtd_buss' => $item->Ps->TC, // Business
                    'qtd_tot' => $item->Ps->TT, // Qtd Total Papers||Contracts
                ];
                if(count($item->BBP->Bd) > 0) {
                    $timeTrade['bid_price'] = $item->BBP->Bd[0]->P;
                    $timeTrade['bid_qtd'] = $item->BBP->Bd[0]->Q;
                }
                if(count($item->BBP->Ak) > 0) {
                    $timeTrade['ask_price'] = $item->BBP->Ak[0]->P;
                    $timeTrade['ask_qtd'] = $item->BBP->Ak[0]->P;
                }
                array_push($this->listTimeTrades, $timeTrade);
                array_push($this->previousTimeTrades, $timeTrade);
            }
    	}
    	TempTimeTrade::insert($this->listTimeTrades);
    }


}