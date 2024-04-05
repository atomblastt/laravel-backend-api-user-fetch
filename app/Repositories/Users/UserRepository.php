<?php

namespace App\Repositories\Users;

use App\Models\Users\User;
use Illuminate\Support\Facades\DB;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UserRepository extends BaseRepository
{
    public function __construct(User $model = new User())
    {
        parent::__construct($model);
    }

    /**
     * Get All
     *
     * @return Collection
     */
    public function getAll($columns = '*', $filter = [])
    {
        $data = $this->model->select($columns);

        if (! empty($filter)) {
            foreach ($filter as $key => $value) {
                $data = $data->orWhere($key, 'like', "%{$value}%");
            }
        }
        $data->orderBy('id', 'desc');
        return $data->get();
    }

    /**
     * Get Where
     *
     * @return Collection
     */
    public function getWhere($wheres = [], $columns = '*', $order_by = null, $order_by_type = null, $limit = null, $relations = [])
    {
        $data = $this->model->select($columns)->where($wheres);
        if ($order_by) {
            $data->orderBy($order_by, $order_by_type);
        }
        if ($limit) {
            $data->take($limit);
        }
        if ($relations) {
            $data->with($relations);
        }
        return $data->get();
    }

    /**
     * findById
     *
     * @return Mixed
     */
    public function findByKey($key, $selects = '*', $relations = null, $sortBy = 'id', $sort = 'asc', $withTrashed = false)
    {
        return $this->findBy(
            key: $key,
            selects: $selects,
            relations: $relations,
            orderBy: $sortBy,
            orderType: $sort,
            withTrashed: $withTrashed
        );
    }

    /**
     * get Average data
     *
     * @return Mixed
     */
    public function getAvg($where = [], $columns = '*', $avg = '')
    {
        return $this->model->select($columns)->where($where)->avg($avg);
    }

    /**
     * Create
     *
     * @return Model
     */
    public function createData($data)
    {
        return DB::transaction(function () use ($data) {
            return $this->create($data);
        });
    }

    /**
     * Update Or Create New Data if data not found
     *
     * @return mixed
     */
    public function updateOrCreate(array $keys, array $data)
    {
        DB::transaction(function () use ($keys, $data) {
            return $this->model->updateOrCreate($keys, $data);
        });
    }

    /**
     * Soft Delete By Id
     *
     * @param int $id
     *
     * @return Model|null
     */
    public function softDeleteById(int $id): ?Model
    {
        try {
            $data = $this->model->findOrFail($id);
            $data->delete();
            return $data;
        } catch (ModelNotFoundException $e) {
            return null;
        }
    }
}
