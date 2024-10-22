<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'students_id',
        'invoice_number',
        'amount',
        'due_date',
        'payment_date',
        'processed_by',
        'paid_month',
        'paid_year',
        'status',
        'issue_date',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'students_id');
    }
}
