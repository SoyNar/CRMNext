<!DOCTYPE html>
<html>
<head>
    <title>Mini CRM - Auth</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen flex items-center justify-center bg-gradient-to-br from-slate-100 via-blue-50 to-indigo-100">

<div class="w-full flex justify-center px-4">

    <div class="w-full max-w-md">

        <!-- SLOT -->
        @yield('content')

    </div>

</div>

</body>
</html>
