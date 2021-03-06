<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TempTimeTrade extends Model
{
    public $timestamps = false;
    protected $fillable = ['unix_timestamp', 'ticker', 'bank_code_purchase', 'bank_code_sale', 'price', 'qtd', 'qtd_buss', 'qtd_tot', 'bid_price', 'bid_qtd', 'ask_price', 'ask_qtd'];
}
