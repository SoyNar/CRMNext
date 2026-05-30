<?php

namespace App\Services\InterfaceService;

interface IClientService
{

    public function all(array $filters = []);
    public function create(array $data);

    public function delete(int $id);

    public function update(int $id, array $data);
    public  function getContactsByClientId(int $id);

    public function getById(int $id);

}
