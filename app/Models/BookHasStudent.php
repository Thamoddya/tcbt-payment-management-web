<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookHasStudent extends Model
{
    use HasFactory;

    protected $fillable = [
        'book_id',
        'student_id',
        'borrowed_at',
        'returned_at',
        'status',
    ];

    protected $dates = ['borrowed_at', 'returned_at'];

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

}
