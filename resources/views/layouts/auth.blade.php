<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title') - {{ env('APP_NAME') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {!! json_encode(config('tailwind')) !!}
    </script>
</head>
<body>
    
<div class="fixed top-0 left-0 bottom-0 w-7/12 bg-cyan-500 mobile:hidden flex flex-col items-center justify-center text-white">
    <h1 class="text-4xl font-black">{{ env('APP_NAME') }}</h1>
</div>
<div class="fixed top-0 right-0 bottom-0 w-5/12 flex flex-col items-center justify-center mobile:w-full">
    @yield('content')
</div>

@yield('javascript')

</body>
</html>