<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <link rel="icon" href="{{ asset('image/TTA logo.jpg') }}" type="image/x-icon">
        <title>TTA</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/css/form.css'])
    </head>
    <body class="font-sans text-black-900 antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-black-100">
            <div class="flex items-center justify-center mb-4">
                <a class="flex items-center">
                    <img src="{{ asset('image/TTA logo.jpg') }}" alt="Your Logo" class="h-20 w-auto mr-2"> <!-- Adjust size as needed -->
                    <span class="text-lg font-bold">THAM'S TAEKWONDO ACADEMY</span>
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white sm::shadow-md overflow-hidden sm:rounded-lg">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
