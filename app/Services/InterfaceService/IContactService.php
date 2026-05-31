<?php

namespace App\Services\InterfaceService;

interface IContactService
{

    public function all(int $clientId, array $filters = []);
    public function findById(int $id);
    public function create(array $data, int $clientId);
    public function update(array $data,int $id);

    public function delete(int $id);

}
