<?php

namespace App\Services\Impl;

use App\Models\Client;
use App\Services\InterfaceService\IClientService;
use App\Traits\Filterable;
use Carbon\Carbon;

class ClientsServiceImpl implements IClientService
{
    use Filterable;

    public function all(array $filters = [])
    {
        $query = Client::query();
        $this->applySearch($query, $filters['search'] ?? null, ['name', 'email']);
        $this->applyFilters($query, $filters, ['status']);
        return $query
            ->with('creator')
            ->withCount('contacts')
            ->orderBy('created_at', 'desc')
            ->paginate($filters['per_page'] ?? 15);
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
        $client = Client::findOrFail($id);
        $client->delete();
        return $client;
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
        return Client::with(['contacts', 'creator'])
            ->select('id','name',
                'nit',
                'phone',
                'url_page',
                'status',
                'created_at',
                'created_by',
                'updated_by',)
            ->findOrFail($id);

    }
}
