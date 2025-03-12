<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\User;
use App\Models\SchoolClass;
use App\Models\SchoolSubject;
use App\DataTables\UserDataTable;
use App\Http\Requests\UserSearchRequest;
use App\Repositories\UserRepository;
use Inertia\Inertia;

class UserController extends Controller
{
    private $repository;

    public function __construct(UserRepository $userRepository){
        $this->repository = $userRepository;
    }

    public function search(UserSearchRequest $request)
    {
        $searchResult = $this->repository->search($request->all());

        return response()->json($searchResult, 201);
    }

}