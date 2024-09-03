<?php
namespace App\Repository;

interface ClientRepository
{
    /**
     * Find a client by their telephone number.
     *
     * @param string $telephone
     * @return \App\Models\Client|null
     */
    public function findByTelephone(string $telephone);

    /**
     * Create a new client.
     *
     * @param array $data
     * @return \App\Models\Client
     */
    public function create(array $data);
    public function createUser(array $data);

    /**
     * Find a client by their ID.
     *
     * @param int $id
     * @return \App\Models\Client|null
     */
    public function findById(int $id);

    /**
     * Get all clients with optional filters.
     *
     * @param array $filters
     * @return \Illuminate\Support\Collection
     */
    public function getAll(array $filters = []);

    /**
     * Update an existing client by their ID.
     *
     * @param int $id
     * @param array $data
     * @return \App\Models\Client|null
     */
    public function update(int $id, array $data);

    /**
     * Delete a client by their ID.
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id);
    public function storePhoto($photo);
}
