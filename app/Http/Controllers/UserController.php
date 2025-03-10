<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\User;
use App\Models\SchoolClass;
use App\Models\SchoolSubject;
use App\DataTables\UserDataTable;
use App\Http\Requests\UserSearchRequest;
use Inertia\Inertia;

class UserController extends Controller
{
    public function search(UserSearchRequest $request)
    {
        $dataTable = new UserDataTable();
        $dataTable->setRequest($request);

        $attendances = $dataTable->query(new User())->get();

        return response()->json([
            'data' => $attendances,
            'total' => $dataTable->getTotal(),
        ], 201);
    }

}