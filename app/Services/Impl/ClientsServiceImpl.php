<?php

namespace App\Services\Impl;

use App\Models\Client;
use App\Services\InterfaceService\IClientService;
use Carbon\Carbon;

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
         return Client::create([
             'name' => $data['name'],
             'phone' => $data['phone'],
             'nit' => $data['nit'],
             'url_page' => $data['url_page'],
             'status' => $data['status'],
             'created_by' => auth()->user()->id,
         ]);
    }

    public function delete(int $id)
    {
        // TODO: Implement delete() method.
    }

    public function update(int $id, array $data)
    {
         $query = Client::query();
        $client = $query->findOrFail($id);
        $client->update($data);
       return  $client;
    }

    public  function getContactsByClientId(int $id)
    {
        return Client::with('contacts')->findOrFail($id);

    }

    public function getById(int $id)
    {
        return Client::with(['contacts','creator'])->findOrFail($id);

    }
}
