<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $primaryKey = 'payment_id';

    protected $fillable = [
        'students_id',
        'amount',
        'payment_date',
        'processed_by',
        'paid_month',
        'paid_year',
        'status',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'students_id');
    }

}
