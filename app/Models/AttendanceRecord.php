<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Validation\Rule;

class AttendanceRecord extends Model
{
    public const STATUS_ON_TIME = 'on-time';
    public const STATUS_LATE = 'late';
    public const STATUS_ABSENT = 'absent';

    use HasFactory;

    protected $fillable = [
        'user_id',
        'teacher_id',
        'school_class_id',
        'school_subject_id',
        'grade',
        'date',
        'status',
        'minutes_late',
        'reason',
    ];

    protected $casts = [
        'date' => 'date:Y-m-d',
        'minutes_late' => 'integer',
    ];

    public static function rules()
    {
        return [
            'user_id' => ['required', 'exists:users,id'],
            'teacher_id' => ['required', 'exists:users,id'],
            'school_class_id' => ['required', 'exists:school_classes,id'],
            'school_subject_id' => ['required', 'exists:school_subjects,id'],
            'grade' => ['required', 'string'],
            'date' => ['required', 'date'],
            'status' => ['required', Rule::in(['on-time', 'late', 'absent'])],
            'minutes_late' => ['nullable', 'integer', 'min:0'],
            'reason' => ['nullable', 'string'],
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class);
    }

    public function schoolSubject()
    {
        return $this->belongsTo(SchoolSubject::class);
    }
}