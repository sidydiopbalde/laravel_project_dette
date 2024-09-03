<?php
namespace App\Services;


use App\Services\ClientService;
use App\Repository\ClientRepository;
use Illuminate\Support\Facades\DB;
use App\Facades\ClientRepositoryFacade;
class ClientServiceImpl implements ClientService
{
    protected $clientRepository;

    // public function __construct(ClientRepository $clientRepository)
    // {
    //     $this->clientRepository = $clientRepository;
    // }

    public function getClientByPhone(string $phone)
    {
        return ClientRepositoryFacade::findByTelephone($phone);
    }

    public function createClient(array $data)
    {
        return DB::transaction(function () use ($data) {
            $user = null;

            if (isset($data['user'])) {
                $userData = $data['user'];
                $userData['password'] = bcrypt($userData['password']);
                $user = ClientRepositoryFacade::createUser($userData);
            }

            $clientData = $data;
            $clientData['user_id'] = $user ? $user->id : null;

            if (isset($data['photo'])) {
                $clientData['photo'] = ClientRepositoryFacade::storePhoto($data['photo']);
            }

            $client = ClientRepositoryFacade::create($clientData);

            if ($user) {
                $client->user()->associate($user);
                $client->save();
            }

            return $client;
        });
    }

    public function getAllClients(array $filters)
    {
        return ClientRepositoryFacade::getAll($filters);
    }

    public function getClientById(int $id, bool $includeUser)
    {
        return ClientRepositoryFacade::findById($id, $includeUser);
    }

    public function findByTelephone(string $telephone)
    {
        return ClientRepositoryFacade::findByTelephone($telephone);
    }

    // Implémentez d'autres méthodes si nécessaire
}
