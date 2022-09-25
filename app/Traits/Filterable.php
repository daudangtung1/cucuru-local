<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait Filterable
{
    /**
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  mixed  $param
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFilter($query, $param)
    {
        foreach ($param as $field => $value) {
            $method = 'filter' . Str::studly($field);

            // TODO: should we use scope to filter ?
            // $this->hasNamedScope(Str::camel($name))
            // $this->scopes($scopeName)
            if ($value === '' || $value === null) {
                continue;
            }

            if (method_exists($this, $method)) {
                $this->{$method}($query, $value);
                continue;
            }

            if (empty($this->filterable) || !is_array($this->filterable)) {
                continue;
            }

            if (in_array($field, $this->filterable)) {
                $query->where($this->table . '.' . $field, $value);
                continue;
            }

            if (key_exists($field, $this->filterable)) {
                $query->where($this->table . '.' . $this->filterable[$field], $value);
                continue;
            }
        }

        return $query;
    }
}
