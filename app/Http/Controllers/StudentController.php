<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\Student;
use App\Models\SchoolClass;
use App\Models\SchoolSubject;
use App\DataTables\StudentDataTable;
use Inertia\Inertia;

class StudentController extends Controller
{

    public function search(Request $request)
    {
        $dataTable = new StudentDataTable();
        $dataTable->setRequest($request);

        $attendances = $dataTable->query(new Student())->get();

        return response()->json([
            'data' => $attendances,
            'total' => $dataTable->getTotal(),
        ], 201);
    }
}