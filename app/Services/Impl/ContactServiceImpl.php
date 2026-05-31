<?php

namespace App\Services\Impl;

use App\Models\Contact;
use App\Services\InterfaceService\IContactService;
use App\Traits\Filterable;

class ContactServiceImpl  implements IContactService
{
    use Filterable;

    public function all(int $clientId, array $filters = [])
    {
        $query = Contact::query()
            ->where('client_id', $clientId)
            ->select([
                'id', 'client_id', 'first_name', 'last_name', 'position',
                'email', 'phone', 'is_primary', 'created_by',
            ]);

        $this->applySearch($query, $filters['search'] ?? null, ['first_name', 'last_name', 'email']);
        $this->applyFilters($query, $filters, ['is_primary']);

        return $query
            ->orderByDesc('is_primary')
            ->orderBy('first_name')
            ->paginate($filters['per_page'] ?? 15);
    }

    public function findById(int $id)
    {
        // TODO: Implement findById() method.
    }

    public function create(array $data, int $clientId)
    {
        if(!empty($data['is_primary'])){
            Contact::where('client_id',$data['client_id'])
                ->where('is_primary',true)
                ->update(['is_primary' => false]);
        }
        return Contact::create([
            'first_name' => $data['first_name'],
            'client_id' => $clientId,
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
             'is_primary' => $data['is_primary'],
            'created_by' =>  auth()->user()->id,
            'position' => $data['position'],

        ]);
    }

    public function update(array $data, int $id)
    {
        // TODO: Implement update() method.
    }

    public function delete(int $id)
    {
        // TODO: Implement delete() method.
    }
}
