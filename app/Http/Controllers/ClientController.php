<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateClientRequest;
use App\Http\Requests\UpdateClientRequest;
use App\Http\Resources\ClientDetailsResource;
use App\Http\Resources\ClientResource;
use App\Http\Resources\ContactResource;
use App\Services\InterfaceService\IClientService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

/**
 * @tags
 * Clientes controller
**/
class ClientController extends Controller
{
    use ApiResponse;
    public function __construct(private IClientService $clientService)
    {

    }

    /**
     * Listar clientes
    **/
    public function list(Request $request)
    {
        $validated = $request->validate([
            'search' => 'nullable|string',
            'status' => ['nullable', 'string'],
        ]);
        $response = $this->clientService->all($validated);
        return $this->respond(ClientResource::collection($response));
    }

    public function index()
    {
        return view('clients.dashboard');
    }

    public function store(CreateClientRequest $request)
    {
        $validated = $request->validated();
        $response = $this->clientService->create($validated);
        return $this->respond(new ClientResource($response));

    }
    public function update(UpdateClientRequest $request, int$id)
    {
        $validated = $request->validated();
        $response = $this->clientService->update($id, $validated);
        return $this->respond(new ClientResource($response));
    }

    public function getContacts(int $clientId)
    {
        $response = $this->clientService->getContactsByClientId($clientId);
        return $this->respond(ContactResource::collection($response));

    }

    public function getById(int $id)
    {
        $response = $this->clientService->getById($id);
        return $this->respond(new ClientDetailsResource($response));
    }
}
