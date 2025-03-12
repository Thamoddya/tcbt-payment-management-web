<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = ['student_id', 'date', 'marked_by'];

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'id');
    }

    public function markedBy()
    {
        return $this->belongsTo(User::class, 'marked_by', 'id');
    }
}
