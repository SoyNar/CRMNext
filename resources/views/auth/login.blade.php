@extends('layouts.auth')

@section('content')

    <div class="bg-white rounded-2xl shadow-2xl border border-gray-100 p-8">

        <!-- Header -->
        <div class="text-center mb-6">
            <div class="mx-auto w-12 h-12 bg-blue-600 text-white rounded-xl flex items-center justify-center font-bold shadow-md">
                CRM
            </div>

            <h2 class="text-2xl font-bold text-gray-800 mt-4">
                Iniciar sesión
            </h2>

            <p class="text-sm text-gray-500 mt-1">
                Accede a tu Mini CRM
            </p>
        </div>

        <!-- Form -->
        <form method="POST" action="/login" class="space-y-5">
            @csrf

            <!-- Email -->
            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1">
                    Email
                </label>
                <input
                    type="email"
                    name="email"
                    required
                    placeholder="tuemail@correo.com"
                    class="w-full px-4 py-3 rounded-xl border border-gray-200
                       bg-gray-50 focus:bg-white
                       focus:outline-none focus:ring-2 focus:ring-blue-500
                       transition shadow-sm"
                >
            </div>

            <!-- Password -->
            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1">
                    Password
                </label>
                <input
                    type="password"
                    name="password"
                    required
                    placeholder="••••••••"
                    class="w-full px-4 py-3 rounded-xl border border-gray-200
                       bg-gray-50 focus:bg-white
                       focus:outline-none focus:ring-2 focus:ring-blue-500
                       transition shadow-sm"
                >
            </div>

            <!-- Errores -->
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
                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold
                   py-3 rounded-xl shadow-md hover:shadow-lg
                   transition transform hover:scale-[1.01]"
            >
                Entrar
            </button>

            <!-- Register -->
            <div class="text-center text-sm text-gray-600 mt-4">
                ¿No tienes cuenta?
                <a href="/register" class="text-blue-600 font-semibold hover:underline">
                    Crear cuenta
                </a>
            </div>

        </form>

    </div>

@endsection
