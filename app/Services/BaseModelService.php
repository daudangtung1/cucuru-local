<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;

abstract class BaseModelService
{
    protected Model $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function getModel(): Model
    {
        return $this->model;
    }

    public function get($id): Model
    {
        return $this->model->findOrFail($id);
    }

    public function destroy($id): void
    {
        $model = $this->model->findOrFail($id);

        $model->delete();
    }

    public function create(array $data): Model
    {
        return $this->model->create($data);
    }

    public function list($filters = [])
    {
        return $this->model->filter($filters)->paginate();
    }

    public function all()
    {
        return $this->model->all();
    }
}
