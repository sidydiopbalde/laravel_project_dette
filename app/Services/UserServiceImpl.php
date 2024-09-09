<?php

namespace App\Services;

use App\Repository\UserRepository;
use Exception;
use App\Exceptions\ServiceException;
class UserServiceImpl implements UserService
{
    protected $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function getAllUsers($filters = [])
    {
        return $this->userRepository->getAllUsers($filters);
    }

    public function getUserById($id)
    {
        $user = $this->userRepository->getUserById($id);
        if (!$user) {
            throw new ServiceException('User not found', 404);
        }
        return $user;
    }

    public function createUser(array $data)
    {
        return $this->userRepository->createUser($data);
    }

    public function updateUser($id, array $data)
    {
        $user = $this->userRepository->updateUser($id, $data);
        if (!$user) {
            throw new ServiceException('User not found', 404);
        }
        return $user;
    }

    public function deleteUser($id)
    {
        $user = $this->userRepository->deleteUser($id);
        if (!$user) {
            throw new ServiceException('User not found', 404);
        }
        return $user;
    }
}
