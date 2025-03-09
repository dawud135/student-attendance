<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SchoolClass extends Model {
    use HasFactory, SoftDeletes;

    protected $table = 'school_classes'; // Explicit table name

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
     * Relationship: A class has many students.
     */
    public function students() {
        return $this->hasMany(Student::class, 'grade', 'grade');
    }

    /**
     * Relationship: A class has many subjects.
     */
    public function subjects() {
        return $this->hasMany(SchoolSubject::class, 'grade', 'grade');
    }

    /**
     * Scope to filter by grade.
     */
    public function scopeByGrade($query, $grade) {
        return $query->where('grade', $grade);
    }
}