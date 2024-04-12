@extends('layouts.auth')

@section('title', 'Login Admin')
    
@section('content')
<form action="{{ route('admin.login') }}" class="w-full p-8 flex flex-col gap-4" method="POST">
    @csrf
    <input type="hidden" name="r" value="{{ $request->r }}">
    <h2 class="text-3xl text-left text-slate-700 font-extrabold mb-8 mobile:text-center">Login</h2>
    <input type="text" name="username" required placeholder="Username" class="bg-slate-100 w-full rounded h-14 p-1 ps-3 pe-3 outline-0">
    <input type="password" name="password" required placeholder="Password" class="bg-slate-100 w-full rounded h-14 p-1 ps-3 pe-3 outline-0">
    
    @if ($errors->count() > 0)
        <div class="bg-red-200 p-3 rounded text-red-700 text-sm ">
            @foreach ($errors->all() as $type => $err)
                <li>{{ $err }}</li>
            @endforeach
        </div>
    @endif

    @if ($message != "")
        <div class="bg-green-200 p-3 rounded text-green-700 text-sm">
            {{ $message }}
        </div>
    @endif

    <button class="bg-cyan-500 h-14 rounded text-white">Login</button>
</form>
@endsection