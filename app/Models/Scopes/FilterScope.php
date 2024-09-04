<?php
namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class FilterScope implements Scope
{
    protected $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function apply(Builder $builder, Model $model)
    {
        if (!empty($this->filters['telephone'])) {
            $builder->where('telephone', 'like', '%' . $this->filters['telephone'] . '%');
        }

        if (!empty($this->filters['surnom'])) {
            $builder->where('surnom', 'like', '%' . $this->filters['surnom'] . '%');
        }

        if (!empty($this->filters['active'])) {
            $builder->where('active', $this->filters['active']);
        }
    }
}


