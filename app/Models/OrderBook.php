<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderBook extends Model
{
    use HasFactory;
    protected $table = 'book_orders';
    protected $fillable =[
        'book_id',
        'user_id',

    ];
}
