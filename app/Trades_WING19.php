<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Trades_WING19 extends Model
{
    protected $table = 'trades_wing19';
    
    public $timestamps = false;
    
    protected $fillable = ['unix_timestamp_open','unix_timestamp_close','open','minimum','maximum','close','volume_purchase','volume_sale'];
    
}
