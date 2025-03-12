<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends Model {
    use HasFactory, SoftDeletes;

    protected $table = 'students'; // Explicit table name

    protected $fillable = [
        'user_id',
        'nis',
        'grade',
        'reward_points',
        'violation_points',
    ];

    protected $casts = [
        'grade' => 'integer',
        'reward_points' => 'integer',
        'violation_points' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $appends = [
        'name',
    ];

    public function getNameAttribute() {
        return $this->user?->name;
    }

    /**
     * Relationship: Student belongs to a User (who is a student).
     */
    public function user() {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship: Student has many attendance records.
     */
    public function attendanceRecords() {
        return $this->hasMany(AttendanceRecord::class);
    }

    /**
     * Relationship: Student belongs to a school class.
     */
    public function schoolClass() {
        return $this->belongsTo(SchoolClass::class, 'grade', 'grade'); // Mapping grade to class
    }

    /**
     * Scope to filter students by grade.
     */
    public function scopeByGrade($query, $grade) {
        return $query->where('grade', $grade);
    }

    /**
     * Get the student's full information.
     */
    public function getFullInfoAttribute() {
        return "{$this->user->name} (NIS: {$this->nis}, Grade: {$this->grade})";
    }
}