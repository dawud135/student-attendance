<?php

namespace App\DataTables;

use App\Models\AttendanceRecord;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

use Inertia\Inertia;

class AttendanceRecordDataTable extends DataTable
{
    public $student;
    public $page = 1;
    public $perPage;
    public $search;

    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('action', function ($row) {
                // Pass any required data to the front-end
                return view('attendance_record/partials/action', [
                    'row' => $row,
                ]);
            })
            ->setRowId('id');
    }

    private function getBaseQuery(AttendanceRecord $model): QueryBuilder
    {
        $query = $model->select($this->getSelects())
                    ->join('users', 'users.id', 'attendance_records.user_id')
                    ->join('school_classes', 'school_classes.id', 'attendance_records.school_class_id')
                    ->join('school_subjects', 'school_subjects.id', 'attendance_records.school_subject_id')    
                    ->join('users as teachers', 'teachers.id', 'attendance_records.teacher_id');

        if(!empty($this->student)){
            $query->where('attendance_records.student_id', $this->student->id);
        }
        
        if(!empty($this->search) && !empty($this->search['value'])){
            $query->where(function($query){
                $query->where('users.name', 'like', '%' . $this->search['value'] . '%')  
                ->orWhere('users.email', 'like', '%' . $this->search['value'] . '%')
                ->orWhere('school_classes.name', 'like', '%' . $this->search['value'] . '%')
                ->orWhere('school_subjects.name', 'like', '%' . $this->search['value'] . '%');
            });
        }   

        return $query;
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(AttendanceRecord $model): QueryBuilder
    {
        $query = $this->getBaseQuery($model);

        if(!empty($this->page) && !empty($this->perPage)){
            $query->skip(($this->page - 1) * $this->perPage)->take($this->perPage);
        }

        return $query;
    }

    public function getTotal(AttendanceRecord $model)
    {
        return $this->getBaseQuery($model)->count();
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
                    ->setTableId('AttendanceRecord-table')
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    //->dom('Bfrtip')
                    ->orderBy(2, 'desc')
                    ->selectStyleSingle()
                    ->buttons([
                        Button::make('excel'),
                        // Button::make('csv'),
                        Button::make('pdf'),
                        Button::make('print'),
                        // Button::make('reset'),
                        // Button::make('reload')
                    ]);
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        return [
            Column::make('date')
                    ->title('Tanggal')
                    ->data('date'),
            Column::make('users.name as student_name')
                    ->title('Nama Siswa')
                    ->data('student_name'),
            Column::make('attendance_records.grade')
                    ->title('Tingkat')
                    ->data('grade'),
            Column::make('school_subjects.name as subject_name')
                    ->title('Mata Pelajaran')
                    ->data('subject_name'),
            Column::make('teachers.name as teacher_name')
                    ->title('Guru')
                    ->data('teacher_name'),
            Column::make('school_classes.name as class_name')
                    ->title('Kelas')
                    ->data('class_name'),
            
            Column::make('status')
                    ->title('Status')
                    ->data('status'),
            Column::make('minutes_late')
                    ->title('Terlambat (menit)')
                    ->data('minutes_late'),
            Column::computed('action')
                  ->name('attendance_records.id')
                  ->exportable(false)
                  ->printable(false)
                  ->sortable(false)
                  ->width(60)
                  ->addClass('text-center')
                  ->title('#'),
        ];
    }

    public function getSelects()
    {
        $selects = collect($this->getColumns())->filter(function($item, $key){
            return !is_numeric($item) && !empty($item->name);
        })->pluck('name')->map(function($item){
            return DB::raw($item);
        })->toArray();

        return $selects;
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'AttendanceRecord_' . date('YmdHis');
    }

    public function setRequest($request)
    {
        $this->page = $request->get('page', 1);
        $this->perPage = $request->get('per_page', 10);
        $this->search = $request->get('search', null);
    }
}
