<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Contact;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $user = User::firstOrCreate(
            ['email' => 'demo@crm.test'],
            [
                'name'     => 'Demo User',
                'password' => Hash::make('password'),
            ]
        );

        Client::factory(10)->create([
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ])->each(function ($client) use ($user) {
            // 3 contactos por cliente
            $contacts = Contact::factory(3)->create([
                'client_id'  => $client->id,
                'created_by' => $user->id,
                'updated_by' => $user->id,
            ]);
            // El primero es el contacto primario
            $contacts->first()->update(['is_primary' => true]);
        });
    }
}
