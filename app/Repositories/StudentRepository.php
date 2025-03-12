<?php
namespace App\Repositories;

use App\Models\Student;

/**
 * Class StudentRepository
 * @package App\Repositories
 * @version October 16, 2019, 6:48 am WIB
*/

class StudentRepository extends BaseRepository
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
        return Student::class;
    }


    public function search($search, $limit = 10) {
        $query = Student::with('user')->join('users', 'users.id', 'students.user_id');

        if(!empty($search)){
            $query->where('users.name', 'like', '%' . $search . '%');
            $query->orWhere('users.email', 'like', '%' . $search . '%');
            $query->orWhere('students.nis', 'like', '%' . $search . '%');
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
}
