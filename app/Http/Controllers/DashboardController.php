<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\AttendanceRecordRepository;
use Inertia\Inertia;
use Carbon\Carbon;

class DashboardController extends Controller
{
    private AttendanceRecordRepository $attendanceRecordRepository;

    public function __construct(
        AttendanceRecordRepository $attendanceRecordRepository, 
    ) {
        $this->attendanceRecordRepository = $attendanceRecordRepository;
    }

    public function index(Request $request)
    {
        $startDt = Carbon::now()->startOfYear();
        if($request->has('startDt')) {
            $startDt = Carbon::parse($request->startDt);
        }

        $endDt = Carbon::now()->endOfMonth();
        if($request->has('endDt')) {
            $endDt = Carbon::parse($request->endDt);
        }

        $latePerMonth = $this->attendanceRecordRepository->qtyPerMonth(['status' => 'late', 'startDt' => $startDt, 'endDt' => $endDt]);
        $latePerMonthChartData = [
            'labels' => $latePerMonth->pluck('month'),
            'datasets' => [
                [
                    'label' => 'Quantity of Late Attendance',
                    'data' => $latePerMonth->pluck('qty'),
                    'backgroundColor' => 'rgba(54, 162, 235, 0.5)',
                    'borderColor' => 'rgba(54, 162, 235, 1)',
                    'borderWidth' => 2,
                ],
            ],
        ];

        $latePerSchoolSubject = $this->attendanceRecordRepository->qtyPerSchoolSubject(['status' => 'late', 'startDt' => $startDt, 'endDt' => $endDt]);
        $latePerSchoolSubjectChartData = [
            'labels' => $latePerSchoolSubject->pluck('school_subject_name'),
            'datasets' => [
                [
                    'label' => 'Late Attendance per Subject',
                    'data' => $latePerSchoolSubject->pluck('qty'),
                    'backgroundColor' => 'rgba(11, 103, 165, 0.5)',
                    'borderColor' => 'rgb(43, 155, 230)',
                    'borderWidth' => 2,
                ],
            ],
        ];

        return Inertia::render('dashboard', [
            'latePerMonthChartData' => $latePerMonthChartData,
            'latePerSchoolSubjectChartData' => $latePerSchoolSubjectChartData,
        ]);
    }
}
