<?php
namespace App\Models\Scopes;

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
            $builder->whereHas('user', function ($query) {
                $query->where('active', $this->filters['active']);
            });
        }
        // if (!empty($this->filters['comptes'])) {
        //     $hasUser = $this->filters['comptes'] === 'oui';
        //     $builder->whereHas('user', function ($query) use ($hasUser) {
        //         if (!$hasUser) {
        //             $query->doesntExist();
        //         }
        //     }, $hasUser ? '>' : '=', 0);
        // }
        if (isset($this->filters['comptes'])) {
            if ($this->filters['comptes'] === 'oui') {
                // Clients associés à un utilisateur
                $builder->whereHas('user');
            } elseif ($this->filters['comptes'] === 'non') {
                // Clients non associés à un utilisateur
                $builder->doesntHave('user');
            }
        }
    }
}


