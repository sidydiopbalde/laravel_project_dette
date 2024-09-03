<?php
namespace App\Repository;

use App\Models\Client;
use App\Models\User;
class ClientRepositoryImpl implements ClientRepository
{
    /**
     * Find a client by their telephone number.
     *
     * @param string $telephone
     * @return \App\Models\Client|null
     */
    public function findByTelephone(string $telephone)
    {
        return Client::where('telephone', $telephone)->first();
    }

    /**
     * Create a new client.
     *
     * @param array $data
     * @return \App\Models\Client
     */
    public function create(array $data)
    {
        return Client::create($data);
    }

    public function store(array $data)
    {
        return Client::create($data);
    }
    public function createUser(array $data)
    {
        return User::create($data);
    }
    /**
     * Find a client by their ID.
     *
     * @param int $id
     * @return \App\Models\Client|null
     */
    public function findById(int $id)
    {
        return Client::find($id);
    }

    /**
     * Get all clients with optional filters.
     *
     * @param array $filters
     * @return \Illuminate\Support\Collection
     */
    public function getAll(array $filters = [])
    {
        $query = Client::query();

        // Apply filters, if any
        if (!empty($filters['telephone'])) {
            $query->where('telephone', 'like', '%' . $filters['telephone'] . '%');
        }

        if (!empty($filters['surnom'])) {
            $query->where('surnom', 'like', '%' . $filters['surnom'] . '%');
        }

        if (!empty($filters['active'])) {
            $query->where('active', $filters['active']);
        }

        return $query->get();
    }

    /**
     * Update an existing client by their ID.
     *
     * @param int $id
     * @param array $data
     * @return \App\Models\Client|null
     */
    public function update(int $id, array $data)
    {
        $client = $this->findById($id);

        if ($client) {
            $client->update($data);
            return $client;
        }

        return null;
    }

    /**
     * Delete a client by their ID.
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id)
    {
        $client = $this->findById($id);

        if ($client) {
            return $client->delete();
        }

        return false;
    }
    /**
     * Store a photo in the designated storage.
     *
     * @param \Illuminate\Http\UploadedFile $photo
     * @return string The path to the stored photo
     */
    public function storePhoto($photo)
    {
        return $photo->store('photos', 'public');
    }
}
