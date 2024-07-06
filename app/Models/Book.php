<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Book extends Model
{
    use HasFactory,SoftDeletes;
    protected $fillable =[
        'id',
        'section_id',
        'title',
        'description',
        'book_file_path',
        'status'
    ];
    public function section()
    {
        return $this->belongsTo(Sections::class,'section_id');
    }
    public function ratings()
    {
        return $this->hasMany(Ratings::class);
    }
}
