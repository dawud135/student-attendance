<?php
namespace App\Repositories;

use App\Models\AttendanceRecord;
use Illuminate\Support\Collection;
use Carbon\Carbon;

/**
 * Class AttendanceRecordRepository
 * @package App\Repositories
 * @version October 16, 2019, 6:48 am WIB
*/

class AttendanceRecordRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'name',
        'email',
        'nis',
        'phone',
        'address',
        'city',
    ];

    /**
     * Return searchable fields
     *
     * @return array
     */
    public function getFieldsSearchable()
    {
        return $this->fieldSearchable;
    }

    /**
     * Configure the Model
     **/
    public function model()
    {
        return AttendanceRecord::class;
    }


    public function search($search, $limit = 10) {
        $query = AttendanceRecord::with('user')->join('users', 'users.id', 'attendance_records.user_id');

        if(!empty($search)){
            $query->where('users.name', 'like', '%' . $search . '%');
            $query->orWhere('users.email', 'like', '%' . $search . '%');
            $query->orWhere('attendance_records.nis', 'like', '%' . $search . '%');
        }

        if(!empty($limit)){
            $query->limit($limit);
        } else {
            $query->limit(10);
        }

        return
        [
            'data' => $query->limit($limit ?? 10)->get(),
            'total' => $query->count(),
        ];
    }

    public function qtyPerMonth($params) : Collection {
        $results = [];

        // Ensure valid date range
        $startDt = Carbon::now()->startOfYear();
        if (isset($params['startDt'])) {
            $startDt = Carbon::parse($params['startDt']);
        }

        $endDt = Carbon::now()->endOfMonth();
        if (isset($params['endDt'])) {
            $endDt = Carbon::parse($params['endDt']);
        }

        // Generate full range of months
        $current = $startDt->copy();
        while ($current->lte($endDt)) {
            $month = $current->format('F');
            $results[$month] = [
                'month' => $month,
                'qty'   => 0,
            ];
            $current->addMonth()->firstOfMonth();
        }

        $query = AttendanceRecord::query();

        if(isset($params['status'])) {
            $query->where('status', $params['status']);
        }

        if(!empty($startDt)) {
            $query->where('date', '>=', $startDt);
        }

        if(!empty($endDt)) {
            $query->where('date', '<=', $endDt);
        }

        $data = $query->selectRaw('MONTHNAME(date) as month, COUNT(1) as qty')
            ->groupBy('month')
            ->get()
            ->keyBy(fn ($item) => $item->month);  // Use month name as the key

        // Merge actual data into the complete month list
        foreach ($results as $key => &$monthData) {
            if (isset($data[$key])) {
                $monthData['qty'] = $data[$key]->qty;
            }
        }

        return collect(array_values($results));
    }

    public function qtyPerSchoolSubject($params) : Collection {
        $results = [];

        $startDt = Carbon::now()->startOfYear();
        if (isset($params['startDt'])) {
            $startDt = Carbon::parse($params['startDt']);
        }

        $endDt = Carbon::now()->endOfMonth();
        if (isset($params['endDt'])) {
            $endDt = Carbon::parse($params['endDt']);
        }

        $query = AttendanceRecord::query();

        if(isset($params['status'])) {
            $query->where('status', $params['status']);
        }

        if(!empty($startDt)) {
            $query->where('date', '>=', $startDt);
        }

        if(!empty($endDt)) {
            $query->where('date', '<=', $endDt);
        }

        $data = $query->selectRaw('school_subject_id, coalesce(school_subjects.name, "Unknown") as school_subject_name, COUNT(*) as qty')
            ->leftJoin('school_subjects', 'school_subjects.id', 'attendance_records.school_subject_id')
            ->groupBy(['attendance_records.school_subject_id', 'school_subject_name'])
            ->orderBy('school_subjects.name', 'desc')
            ->get();    

        return $data;
    }
}
