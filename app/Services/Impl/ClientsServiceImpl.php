<?php

namespace App\Services\Impl;

use App\Models\Client;
use App\Services\InterfaceService\IClientService;

class ClientsServiceImpl implements IClientService
{

    public function all(array $filters = [])
    {
        $query = Client::query();
        if (!empty($filters['search'])) {
           $query->where(function ($q) use ($filters) {
               $q->where('name', 'like', '%' . $filters['search'] . '%');
               $q->orWhere('email', 'like', '%' . $filters['search'] . '%');
           });
        }
        if(!empty($filters['status'])){
            $query->where('status', $filters['status']);
        }

        $query->with('creator')
            ->withCount('contacts')
            ->orderBy('created_at','desc');
        return $query->paginate(15);
    }

    public function create(array $data)
    {
        // TODO: Implement create() method.
    }

    public function delete(int $id)
    {
        // TODO: Implement delete() method.
    }

    public function update(int $id, array $data)
    {
        // TODO: Implement update() method.
    }
}
