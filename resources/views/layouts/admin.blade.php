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
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <style>
        * { transition: 0.4s; }
    </style>
    <link rel="stylesheet" href="{{ asset('css/user.css') }}">
    @yield('head.dependencies')
</head>
<body class="bg-gray-100">
    
<div class="fixed top-0 left-0 right-0 h-16 bg-white border-b z-10 flex flex-row items-center gap-4 ps-8 pe-8 Header">
    <div class="h-10 aspect-square rounded flex items-center justify-center cursor-pointer" onclick="ToggleSidebar()">
        <i class="bx bx-menu text-2xl"></i>
    </div>
    <div class="text-lg mobile:text-sm font-bold text-gray-900 flex grow">@yield('title')</div>
    @yield('header.right')
</div>

<div class="Container absolute top-16 bottom-0 right-0 flex flex-row desktop:sidebar-on">
    @php
        $routeName = Route::currentRouteName();
    @endphp
    <aside class="bg-white border-r p-8 flex flex-col top-16 Sidebar">
        <div class="flex flex-row gap-4 items-center mb-4">
            <div class="h-12 aspect-square rounded-full bg-cyan-500 flex items-center justify-center font-bold text-white">
                {{ initial($admin->name) }}
            </div>
            <div class="flex flex-col gap-2 grow">
                <div class="text-sm">{{ $admin->name }}</div>
                <div class="flex">
                    <a href="{{ route('admin.logout') }}" class="bg-red-100 hover:bg-red-400 hover:text-white rounded cursor-pointer p-2 ps-3 pe-3 text-xs text-red-400">
                        Logout
                    </a>
                </div>
            </div>
        </div>

        <a href="{{ route('admin.dashboard') }}" class="flex flex-row gap-4 items-center p-2 ps-4 pe-4 menu-item border-l-4 {{ $routeName == 'admin.dashboard' ? 'border-cyan-400 text-cyan-700' : 'border-white' }}">
            <i class="bx bx-home text-xl"></i>
            <div class="text-sm">Dashboard</div>
        </a>
        
        <div class="h-8"></div>

        <div class="text-sm text-slate-500 mb-4">Data</div>
        <a href="{{ route('admin.ad') }}" class="flex flex-row gap-4 items-center p-2 ps-4 pe-4 menu-item border-l-4 {{ like('layanan', $routeName) ? 'border-cyan-400 text-cyan-700' : 'border-white' }}">
            <i class="bx bx-list-ul text-xl"></i>
            <div class="text-sm">Iklan</div>
        </a>
    </aside>
    <div class="content">
        @yield('content')
    </div>
</div>

<script>
    const Container = document.querySelector(".Container");
    const Content = document.querySelector(".content");
    const ScreenWidth = screen.width;

    const ToggleSidebar = () => {
        Container.classList.toggle('sidebar-on');
        Content.classList.toggle('sidebar-on');
    }
    const toggleModal = target => {
        const modal = select(target);
        if (modal.classList.contains('hidden')) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        } else {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
    }
    const select = selector => document.querySelector(selector);

    const readFileName = (input, target) => {
        let file = input.files[0];
        select(target).innerText = file.name;
    }
    const readImageFromInput = (input, target, callback = null) => {
        let file = input.files[0];
        let reader = new FileReader();
        let preview = select(target);
        reader.readAsDataURL(file);

        reader.addEventListener("load", () => {
            preview.style.color = "rgba(0,0,0,0.01)";
            preview.style.backgroundImage = `url(${reader.result})`;
            preview.style.backgroundSize = "cover";
            preview.style.backgroundRepeat = "no-repeat";
            preview.style.backgroundPosition = "center center";

            if (callback !== null) {
                callback();
            }
        })
    }

    if (ScreenWidth > 1024) {
        ToggleSidebar();
    }
</script>

@yield('javascript')

</body>
</html>