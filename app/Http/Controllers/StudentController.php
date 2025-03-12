<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\Student;
use App\Models\SchoolClass;
use App\Models\SchoolSubject;
use App\DataTables\StudentDataTable;
use App\Http\Requests\StudentSearchRequest;
use Inertia\Inertia;
use App\Repositories\StudentRepository;

class StudentController extends Controller
{
    private $repository;

    public function __construct(StudentRepository $studentRepository){
        $this->repository = $studentRepository;
    }


    public function search(StudentSearchRequest $request)
    {
        $searchResult = $this->repository->search($request->search['value'], $request->limit);

        return response()->json($searchResult, 201);
    }
}