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
use App\Http\Requests\AttendanceRecordFieldRequest;
use Illuminate\Support\Facades\DB;

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

        $response = $dataTable->search($request);

        return response()->json($response, 201);
    }

    public function create()
    {
        $attendanceRecord = new AttendanceRecord;
        $attendanceRecord->teacher_id = auth()->user()->id;
        $attendanceRecord->date = now();
        $attendanceRecord->status = AttendanceRecord::STATUS_LATE;
        
        $classes = SchoolClass::select('id', 'name')->get();
        $subjects = SchoolSubject::select('id', 'name')->get();
        
        return inertia('AttendanceRecord/Create', [
            'attendanceRecord' => $attendanceRecord,
            'classes' => $classes,
            'subjects' => $subjects,
        ]);
    }

    // Store a new attendance record
    public function store(AttendanceRecordFieldRequest $request)
    {
        try {
            DB::beginTransaction();

            $attendance = AttendanceRecord::create($request->all());
            
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput();
        }
        
        return redirect()->route('attendance-record.index')->with('success', 'Attendance record has been created successfully.');
    }

    public function edit($id)
    {
        $attendanceRecord = AttendanceRecord::findOrFail($id);
        $attendanceRecord->load('user', 'teacher', 'schoolClass', 'schoolSubject');

        $classes = SchoolClass::select('id', 'name')->get();
        $subjects = SchoolSubject::select('id', 'name')->get();
        
        return inertia('AttendanceRecord/Edit', [
            'attendanceRecord' => $attendanceRecord,
            'classes' => $classes,
            'subjects' => $subjects,
        ]);
    }

    public function update(Request $request, $id)
    {
        $attendance = AttendanceRecord::findOrFail($id);

        try {
            DB::beginTransaction();

            $attendance->update($request->all());
            
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput();
        }
        
        return redirect()->route('attendance-record.index')
        ->with('success', 'Attendance record has been updated successfully.');
    }
}