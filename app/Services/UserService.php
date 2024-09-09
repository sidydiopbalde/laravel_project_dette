<?php

namespace App\Services;

interface UserService
{
    public function getAllUsers($filters = []);

    public function getUserById($id);

    public function createUser(array $data);

    public function updateUser($id, array $data);

    public function deleteUser($id);
}
