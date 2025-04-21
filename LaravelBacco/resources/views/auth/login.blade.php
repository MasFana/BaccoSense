@extends('layouts.app')

@section('content')

<main class="min-h-screen w-full bg-white md:bg-inherit flex items-center justify-center">
    <nav class="absolute top-0 w-full p-2 bg-white shadow-sm">
        <div class="container mx-auto flex justify-between items-center">
            <a href="{{ url('/') }}" class="text-3xl font-semibold"><</a>
            <div class="space-x-4">
            </div>
        </div>

    </nav>
    <div class="w-full h-full mb-16 lg:mb-0 max-w-md bg-white md:shadow-lg p-6 transition-transform duration-300 ease-in-out">
        <!-- Tobacco SVG Icon -->
        <div class="flex justify-center mb-2">
            <svg class="w-12" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
            </svg>
        </div>

        <h1 class="text-2xl font-bold text-center text-gray-800 mb-8">Masuk</h1>
        
        <form action="{{ route('login') }}" method="POST" class="space-y-6">
            @csrf
            <div class="space-y-2">
                <div class="relative">
                    <input 
                        type="text" 
                        name="name" 
                        id="name" 
                        required 
                        class="w-full px-4 py-3 border-b border-gray-300 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-200 ease-in-out"
                        placeholder="username"
                    >
                </div>
            </div>

            <div class="space-y-2">
                <div class="relative">
                    <input 
                        type="password" 
                        name="password" 
                        id="password" 
                        required 
                        class="w-full px-4 py-3 border-b border-gray-300 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-200 ease-in-out"
                        placeholder="password"
                    >
                </div>
            </div>

            <div class="flex items-center justify-between">
                <label class="flex items-center">
                    <input type="checkbox" name="remember" class="w-4 h-4 text-green-600 border-gray-300 rounded focus:ring-green-500">
        
                    <span class="ml-2 text-sm text-gray-600">Ingat Saya</span>
                </label>
                <a href="#" class="text-sm text-blue-600 hover:text-blue-800">Lupa Password</a>
            </div>

            <button 
                type="submit" 
                class="w-full px-4 py-3 text-white font-medium bg-green-600 rounded-lg hover:bg-green-700 focus:outline-none focus:ring-4 focus:ring-green-300 transition-all duration-200 ease-in-out transform hover:-translate-y-0.5"
            >
                Masuk
            </button>
        </form>
    </div>
</main>
@endsection