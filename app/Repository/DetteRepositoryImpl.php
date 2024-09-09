<?php

namespace App\Repository;

use App\Models\Dette;

class DetteRepositoryImpl implements DetteRepository
{
    public function create(array $data)
    {
        return Dette::create($data);
    }

    public function update(int $id, array $data)
    {
        $debt = Dette::find($id);
        if ($debt) {
            $debt->update($data);
            return $debt;
        }
        return null;
    }

    public function delete(int $id)
    {
        $debt = Dette::find($id);
        if ($debt) {
            return $debt->delete();
        }
        return false;
    }

    public function findById(int $id)
    {
        return Dette::find($id);
    }

    public function findByClient(int $clientId)
    {
        return Dette::where('client_id', $clientId)->get();
    }
}
