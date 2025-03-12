<?php
namespace App\Repositories;

use App\Models\User;

/**
 * Class UserRepository
 * @package App\Repositories
 * @version October 16, 2019, 6:48 am WIB
*/

class UserRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'name',
        'email',
        'role',
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
        return User::class;
    }


    public function search($payload) {
        $search_text = $payload['search']['value'] ?? null;
        $limit = $payload['limit'] ?? 12;
        $role = $payload['role'] ?? null;

        $query = User::with('student');

        if(!empty($search_text)){
            $query->where(function ($q) use ($search_text) {
                $q->where('users.name', 'like', '%' . $search_text . '%')
                  ->orWhere('users.email', 'like', '%' . $search_text . '%');
            });
        }

        if(!empty($role)){
            $query->whereHas('roles', function ($q) use ($role) {
                $q->where('name', $role);
            });
        }

        return
        [
            'data' => $query->limit($limit)->get(),
            'total' => $query->count(),
        ];
    }
}
