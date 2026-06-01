@extends('layouts.auth')

@section('content')

    <div class="bg-white rounded-2xl shadow-2xl border border-gray-100 p-8">

        <!-- Header -->
        <div class="text-center mb-6">

            <div class="mx-auto w-12 h-12 bg-green-600 text-white rounded-xl flex items-center justify-center font-bold shadow-md">
                CRM
            </div>

            <h2 class="text-2xl font-bold text-gray-800 mt-4">
                Crear cuenta
            </h2>

            <p class="text-sm text-gray-500">
                Regístrate en el Mini CRM
            </p>
        </div>

        <!-- Form -->
        <form method="POST" action="/register" class="space-y-5">
            @csrf

            <!-- Name -->
            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1">
                    Nombre
                </label>
                <input
                    type="text"
                    name="name"
                    required
                    class="w-full px-4 py-3 rounded-xl border border-gray-200
                       bg-gray-50 focus:bg-white
                       focus:outline-none focus:ring-2 focus:ring-green-500
                       transition shadow-sm"
                    placeholder="Tu nombre"
                >
            </div>

            <!-- Email -->
            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1">
                    Email
                </label>
                <input
                    type="email"
                    name="email"
                    required
                    class="w-full px-4 py-3 rounded-xl border border-gray-200
                       bg-gray-50 focus:bg-white
                       focus:outline-none focus:ring-2 focus:ring-green-500
                       transition shadow-sm"
                    placeholder="correo@ejemplo.com"
                >
            </div>

            <!-- Password -->
            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1">
                    Contraseña
                </label>
                <input
                    type="password"
                    name="password"
                    required
                    class="w-full px-4 py-3 rounded-xl border border-gray-200
           bg-gray-50 focus:bg-white
           focus:outline-none focus:ring-2 focus:ring-green-500
           transition shadow-sm"
                    placeholder="••••••••"
                >
                <p class="text-xs text-gray-400 mt-1">
                    Mínimo 8 caracteres, al menos una letra y un número.
                </p>
            </div>

            <!-- Confirmar Password -->
            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1">
                    Confirmar contraseña
                </label>
                <input
                    type="password"
                    name="password_confirmation"
                    required
                    class="w-full px-4 py-3 rounded-xl border border-gray-200
           bg-gray-50 focus:bg-white
           focus:outline-none focus:ring-2 focus:ring-green-500
           transition shadow-sm"
                    placeholder="••••••••"
                >
            </div>

            <!-- Errors -->
            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm">
                    <ul class="list-disc ml-5 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Button -->
            <button
                type="submit"
                class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold
                   py-3 rounded-xl shadow-md hover:shadow-lg
                   transition transform hover:scale-[1.01]"
            >
                Crear cuenta
            </button>

            <!-- Link login -->
            <div class="text-center text-sm text-gray-600 mt-4">
                ¿Ya tienes cuenta?
                <a href="/login" class="text-green-600 font-semibold hover:underline">
                    Iniciar sesión
                </a>
            </div>

        </form>

    </div>

@endsection
