<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\AttendanceRecord;
use App\Models\Student;
use App\Models\SchoolClass;
use App\Models\SchoolSubject;
use App\DataTables\AttendanceRecordDataTable;
use Inertia\Inertia;

class AttendanceRecordController extends Controller
{
    // Fetch all attendance records
    public function index()
    {
        $students = Student::join('users', 'students.user_id', '=', 'users.id')
            ->select('students.id', 'users.name as student_name', 'students.grade')
            ->get();
        $classes = SchoolClass::select('id', 'name')->get();
        $subjects = SchoolSubject::select('id', 'name')->get();

        $dataTable = new AttendanceRecordDataTable();

        return inertia('AttendanceRecord/Index', [
            'students' => $students,
            'classes' => $classes,
            'subjects' => $subjects,
            'columns' => $dataTable->getColumns(),
        ]);
    }

    public function dt(Request $request)
    {
        $dataTable = new AttendanceRecordDataTable();
        $dataTable->setRequest($request);

        $attendances = $dataTable->query(new AttendanceRecord())->get();

        return response()->json([
            'data' => $attendances,
            'total' => $dataTable->getTotal(),
        ], 201);
    }

    public function create()
    {
        $attendanceRecord = new AttendanceRecord;
        $classes = SchoolClass::select('id', 'name')->get();
        $subjects = SchoolSubject::select('id', 'name')->get();
        
        return inertia('AttendanceRecord/Edit', [
            'attendanceRecord' => $attendanceRecord,
            'classes' => $classes,
            'subjects' => $subjects,
        ]);
    }

    // Store a new attendance record
    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => ['required', 'exists:users,id'],
            'subject_id' => ['required', 'exists:school_subjects,id'],
            'teacher_id' => ['required', 'exists:users,id'],
            'grade' => ['required', 'unsignedTinyInteger'],
            'late_minutes' => ['required', 'integer', 'min:0'],
        ]);

        $attendance = AttendanceRecord::create($validated);
        return response()->json($attendance, 201);
    }

    public function edit($id)
    {
        $attendance = AttendanceRecord::findOrFail($id);
        $classes = SchoolClass::select('id', 'name')->get();
        $subjects = SchoolSubject::select('id', 'name')->get();
        
        return inertia('AttendanceRecord/Edit', [
            'attendance' => $attendance,
            'classes' => $classes,
            'subjects' => $subjects,
        ]);
    }

    public function update(Request $request, $id)
    {
        $attendance = AttendanceRecord::findOrFail($id);
        $attendance->update($request->all());
        return response()->json($attendance, 200);
    }
}