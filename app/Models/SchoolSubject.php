<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SchoolSubject extends Model {
    use HasFactory, SoftDeletes;

    protected $table = 'school_subjects'; // Explicit table name

    protected $fillable = [
        'name',
        'grade',
    ];

    protected $casts = [
        'grade' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Relationship: A subject belongs to a school class.
     */
    public function schoolClass() {
        return $this->belongsTo(SchoolClass::class, 'grade', 'grade');
    }

    /**
     * Scope to filter by grade.
     */
    public function scopeByGrade($query, $grade) {
        return $query->where('grade', $grade);
    }
}