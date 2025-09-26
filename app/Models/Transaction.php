<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;





class Transaction extends Model
{
    protected $table = 'transactions';
    protected $fillable = [
        'userid','platform','transactionno','type','amount','amount_1','category','remark','status'
    ];
    public $timestamps = true;
}
