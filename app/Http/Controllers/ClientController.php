<?php

namespace App\Http\Controllers;

use App\Http\Resources\ClientResource;
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
}
