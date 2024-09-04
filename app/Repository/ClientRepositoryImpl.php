<?php
namespace App\Repository;

use App\Exceptions\Repositories\RepositoryException as RepositoriesRepositoryException;
use App\Models\Client;
use App\Models\User;
use App\Exceptions\RepositoryException;
use Illuminate\Support\Facades\Log;

class ClientRepositoryImpl implements ClientRepository
{
    /**
     * Find a client by their telephone number.
     *
     * @param string $telephone
     * @return \App\Models\Client|null
     * @throws \App\Exceptions\Repositories\RepositoryException
     */
    public function findByTelephone(string $telephone)
    {
        try {
            return Client::where('telephone', $telephone)->first();
        } catch (\Exception $e) {
            // Log the exception or handle it as needed
            Log::error('Failed to find client by telephone', ['telephone' => $telephone, 'error' => $e->getMessage()]);
            throw new RepositoryException('Failed to find client by telephone.', 0, $e);
        }
    }

    /**
     * Create a new client.
     *
     * @param array $data
     * @return \App\Models\Client
     * @throws \App\Exceptions\Repositories\RepositoryException
     */
    public function create(array $data)
    {
        try {
            return Client::create($data);
        } catch (\Exception $e) {
            Log::error('Failed to create client', ['data' => $data, 'error' => $e->getMessage()]);
            throw new RepositoryException('Failed to create client.', 0, $e);
        }
    }

    public function store(array $data)
    {
        try {
            return Client::create($data);
        } catch (\Exception $e) {
            Log::error('Failed to store client', ['data' => $data, 'error' => $e->getMessage()]);
            throw new RepositoryException('Failed to store client.', 0, $e);
        }
    }

    public function createUser(array $data)
    {
        try {
            return User::create($data);
        } catch (\Exception $e) {
            Log::error('Failed to create user', ['data' => $data, 'error' => $e->getMessage()]);
            throw new RepositoryException('Failed to create user.', 0, $e);
        }
    }

    /**
     * Find a client by their ID.
     *
     * @param int $id
     * @return \App\Models\Client|null
     * @throws \App\Exceptions\Repositories\RepositoryException
     */
    public function findById(int $id)
    {
        try {
            return Client::find($id);
        } catch (\Exception $e) {
            Log::error('Failed to find client by ID', ['id' => $id, 'error' => $e->getMessage()]);
            throw new RepositoryException('Failed to find client by ID.', 0, $e);
        }
    }

    /**
     * Get all clients with optional filters.
     *
     * @param array $filters
     * @return \Illuminate\Support\Collection
     * @throws \App\Exceptions\Repositories\RepositoryException
     */
    public function getAll(array $filters = [])
    {
        try {
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
        } catch (\Exception $e) {
            Log::error('Failed to get all clients', ['filters' => $filters, 'error' => $e->getMessage()]);
            throw new RepositoryException('Failed to get all clients.', 0, $e);
        }
    }

    /**
     * Update an existing client by their ID.
     *
     * @param int $id
     * @param array $data
     * @return \App\Models\Client|null
     * @throws \App\Exceptions\Repositories\RepositoryException
     */
    public function update(int $id, array $data)
    {
        try {
            $client = $this->findById($id);

            if ($client) {
                $client->update($data);
                return $client;
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Failed to update client', ['id' => $id, 'data' => $data, 'error' => $e->getMessage()]);
            throw new RepositoryException('Failed to update client.', 0, $e);
        }
    }

    /**
     * Delete a client by their ID.
     *
     * @param int $id
     * @return bool
     * @throws \App\Exceptions\Repositories\RepositoryException
     */
    public function delete(int $id)
    {
        try {
            $client = $this->findById($id);

            if ($client) {
                return $client->delete();
            }

            return false;
        } catch (\Exception $e) {
            Log::error('Failed to delete client', ['id' => $id, 'error' => $e->getMessage()]);
            throw new RepositoryException('Failed to delete client.', 0, $e);
        }
    }

    /**
     * Store a photo in the designated storage.
     *
     * @param \Illuminate\Http\UploadedFile $photo
     * @return string The path to the stored photo
     * @throws \App\Exceptions\Repositories\RepositoryException
     */
    public function storePhoto($photo)
    {
        try {
            return $photo->store('photos', 'public');
        } catch (\Exception $e) {
            Log::error('Failed to store photo', ['photo' => $photo, 'error' => $e->getMessage()]);
            throw new RepositoryException('Failed to store photo.', 0, $e);
        }
    }
}
