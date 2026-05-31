@extends('layouts.app')

@section('title', isset($client) ? 'Contactos de ' . $client->name : 'Contactos')

@section('content')
    <div id="contacts-app"
         data-client-id="{{ $client->id ?? '' }}"
         data-client-name="{{ $client->name ?? '' }}">
    </div>
@endsection
