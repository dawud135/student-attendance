<?php

namespace App\DataTables;

use App\Models\Student;
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

class StudentDataTable extends DataTable
{
    public $student;
    public $page = 1;
    public $perPage;

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
                return view('student/partials/action', [
                    'row' => $row,
                ]);
            })
            ->setRowId('id');
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(Student $model): QueryBuilder
    {
        $query = $model->select($this->getSelects())
                    ->join('users', 'users.id', 'students.user_id');

        if(!empty($this->page) && !empty($this->perPage)){
            $query->skip(($this->page - 1) * $this->perPage)->take($this->perPage);
        }

        return $query;
    }

    public function getTotal()
    {
        return $this->query(new Student())->count();
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
                    ->setTableId('Student-table')
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
            Column::make('users.name as student_name')
                    ->title('Nama Siswa')
                    ->data('student_name'),
            Column::make('users.email as student_email')
                    ->title('Email Siswa')
                    ->data('student_email'),
            Column::make('students.nis')
                    ->title('NIS')
                    ->data('nis'),
            Column::make('students.grade')
                    ->title('Tingkat')
                    ->data('grade'),
            Column::computed('action')
                  ->name('students.id')
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
        return 'Student_' . date('YmdHis');
    }

    public function setRequest($request)
    {
        $this->page = $request->get('page', 1);
        $this->perPage = $request->get('per_page', 10);
    }
}
