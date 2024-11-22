<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'tcbt_student_number',
        'name',
        'contact_no',
        'grade',
        'school',
        'address',
        'parent_contact_no',
        'parent_name',
        'status',
        'need_to_pay',
        'registration_date'
    ];

    public function payments()
    {
        return $this->hasMany(Payment::class, 'students_id', 'id');
    }

}
