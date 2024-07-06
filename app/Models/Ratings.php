<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ratings extends Model
{
    use HasFactory;
    protected $fillable = ['book_id', 'rating'];

    public function book()
    {
        return $this->belongsTo(Book::class);
    }
}
