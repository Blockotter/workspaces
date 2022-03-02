<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Laravel</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">

    <!-- Styles -->
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        body {
            font-family: 'Nunito', sans-serif;
        }
    </style>
</head>
<body class="antialiased bg-gray-100">
<div
    class="w-full p-32 text-white bg-cover bg-gray-900/50 bg-blend-darken bg-[url('https://images.unsplash.com/photo-1553028826-f4804a6dba3b?ixlib=rb-1.2.1&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=2070&q=80')]">
    <div>
        <h1 class="text-5xl font-extrabold">
            Find your next workplace
        </h1>
        <h2 class="text-xl font-bold mt-4">
            Join over 3.1 million people worldwide working in coworking spaces.
        </h2>
    </div>

    <form action="/" method="GET" class="mt-8 flex">
        <input class="w-1/2 p-4 rounded-lg bg-white shadow-lg text-black font-bold" type="text"
               placeholder="Try Rotterdam, Berlin or Barcelona!" name="place">
        <button class="p-4 ml-4 rounded-lg bg-white shadow-lg text-black font-bold hover:bg-gray-100">
            Search 🔎
        </button>
    </form>
</div>
<div class="mx-4 mt-4">
    <div class="w-full py-8 pl-4 font-bold bg-[#343298] rounded-lg flex overflow-x-scroll flex-nowrap">
        @foreach($amenities as $amenity)
            <div
                class="shadow-lg flex-shrink-0 p-3 bg-white rounded-lg text-[#343298] border-gray-800 border-2 border-dashed grayscale mx-4">
                {{ $amenity->getAttribute('emoji') }} <span class="ml-2">{{ $amenity->getAttribute('name') }}</span>
            </div>
        @endforeach
    </div>
</div>
<div class="flex flex-wrap justify-center">
    @foreach ($workspaces as $workspace)
        <x-workspace-card :workspace="$workspace"/>
    @endforeach
</div>
</body>
</html>
