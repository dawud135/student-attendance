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

    /**
     * Get the query source of dataTable.
     */
    public function query(AttendanceRecord $model): QueryBuilder
    {
        $query = $model->select($this->getSelects())
                    ->join('students', 'students.id', 'attendance_records.student_id')
                    ->join('users', 'users.id', 'students.user_id')
                    ->join('school_classes', 'school_classes.id', 'attendance_records.school_class_id')
                    ->join('subjects', 'subjects.id', 'attendance_records.subject_id')
                    ->join('teachers', 'teachers.id', 'attendance_records.teacher_id');

        if(!empty($this->student)){
            $query->where('kodeinstansi', $this->student->KodeDetail);
        }

        return $query;
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
            Column::make('grade')
                    ->title('Tingkat')
                    ->data('grade'),
            Column::make('class_subject')
                    ->title('Mata Pelajaran')
                    ->data('class_subject'),
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
}
