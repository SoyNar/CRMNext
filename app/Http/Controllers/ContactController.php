<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateContactRequest;
use App\Http\Resources\ContactResource;
use App\Models\Client;
use App\Services\InterfaceService\IContactService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    use ApiResponse;

    public function __construct(private IContactService $contactService){

    }

    public function list(Client $client)
    {
        return view('contacts.list',   compact('client'));
    }

    public function all(int $clientId, Request $request)
    {
        $filters = $request->validate([
            'search'     => ['nullable', 'string', 'max:100'],
            'is_primary' => ['nullable', 'boolean'],
            'page'       => ['nullable', 'integer', 'min:1'],
            'per_page'   => ['nullable', 'integer', 'min:5', 'max:100'],
        ]);

        return $this->contactService->all($clientId, $filters);
    }

    public function create(CreateContactRequest $request, int $clientId)
    {
        $validated = $request->validated();
        $contact = $this->contactService->create($validated,$clientId);
        return $this->respond(new ContactResource($contact),'contacto creado exitosamente',201);
    }


}
