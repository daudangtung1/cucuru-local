<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;

abstract class BaseService
{
    protected $model;

    public function __construct($id = null)
    {
        $this->model($id);
    }

    public function model($id = null)
    {
        if (!empty($id)) {
            $this->model = $id instanceof Model ? $id : $this->getById($id);
        }
        return $this->model;
    }

    public function clearModel()
    {
        $this->model = null;
    }

    public abstract function getById($id, $strict = true);

    public function getAll()
    {
        return [];
    }

    protected function customPagination($object)
    {
        $total = $object->total();
        $page  = $object->currentPage();
        $limit = $object->perPage();

        return [
            '_current' => $page,
            '_next'    => ($page * $limit) < $total ? $page + 1 : null,
            '_prev'    => $page > 1 ? $page - 1 : null,
            '_last'    => $object->lastPage(),
            '_limit'   => $object->perPage(),
            '_total'   => $total,
        ];
    }
}
