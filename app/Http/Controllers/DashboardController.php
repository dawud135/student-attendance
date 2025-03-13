<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\AttendanceRecordRepository;
use App\Repositories\UserRepository;
use Inertia\Inertia;
use Carbon\Carbon;
use App\Http\Requests\DashboardRequest;

class DashboardController extends Controller
{
    private AttendanceRecordRepository $attendanceRecordRepository;
    private UserRepository $userRepository;

    public function __construct(
        AttendanceRecordRepository $attendanceRecordRepository, 
        UserRepository $userRepository
    ) {
        $this->attendanceRecordRepository = $attendanceRecordRepository;
        $this->userRepository = $userRepository;
    }

    public function index(DashboardRequest $request)
    {
        $year = Carbon::now()->year;
        if($request->has('year')) {
            $year = $request->year;
        }

        $startDt = Carbon::now()->startOfYear();
        if($request->has('startDt')) {
            $startDt = Carbon::parse($request->startDt);
        } else {
            $startDt = Carbon::createFromDate($year, 1, 1);
        }

        $endDt = Carbon::now()->endOfMonth();
        if($request->has('endDt')) {
            $endDt = Carbon::parse($request->endDt);
        } else {
            $endDt = Carbon::createFromDate($year, 12, 31);
        }

        $latePerMonth = $this->attendanceRecordRepository->qtyPerMonth(['status' => 'late', 'startDt' => $startDt, 'endDt' => $endDt, 'user_id' => $request->user_id]);
        $latePerMonthChartData = [
            'labels' => $latePerMonth->pluck('month'),
            'datasets' => [
                [
                    'label' => 'Late Attendance',
                    'data' => $latePerMonth->pluck('qty'),
                    'backgroundColor' => 'rgba(54, 162, 235, 0.5)',
                    'borderColor' => 'rgba(54, 162, 235, 1)',
                    'borderWidth' => 2,
                ],
            ],
        ];

        $userStudent = $this->userRepository->find($request->user_id);
        $latePerSchoolSubject = $this->attendanceRecordRepository->qtyPerSchoolSubject(['status' => 'late', 'startDt' => $startDt, 'endDt' => $endDt, 'user_id' => $request->user_id]);
        $latePerSchoolSubjectChartData = [
            'labels' => $latePerSchoolSubject->pluck('school_subject_name'),
            'datasets' => [
                [
                    'label' => 'Late Attendance',
                    'data' => $latePerSchoolSubject->pluck('qty'),
                    'backgroundColor' => 'rgba(11, 103, 165, 0.5)',
                    'borderColor' => 'rgb(43, 155, 230)',
                    'borderWidth' => 2,
                ],
            ],
        ];

        return Inertia::render('dashboard', [
            'year' => $year,
            'userStudent' => $userStudent,
            'latePerMonthChartData' => $latePerMonthChartData,
            'latePerSchoolSubjectChartData' => $latePerSchoolSubjectChartData,
        ]);
    }
}
