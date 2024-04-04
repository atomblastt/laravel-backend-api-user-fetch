<?php

namespace App\Repositories\Users;

use App\Models\Users\DailyRecord;
use Illuminate\Support\Facades\DB;
use App\Repositories\BaseRepository;

class UserDailyRecordRepository extends BaseRepository
{
    public function __construct(DailyRecord $model = new DailyRecord())
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
     * findById
     *
     * @return Mixed
     */
    public function findByKey($key, $selects = '*', $relations = null, $sortBy = 'id', $sort = 'asc')
    {
        return $this->findBy(
            key: $key,
            selects: $selects,
            relations: $relations,
            orderBy: $sortBy,
            orderType: $sort
        );
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
}
