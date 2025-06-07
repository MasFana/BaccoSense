    <nav class="sticky top-0 z-[999] w-full bg-white p-3 shadow-md ">
        <div class="container mx-auto md:px-6">
            <div class="flex items-center justify-between">
                <a class="font-semibold md:text-3xl" href="{{ route('dashboard') }}">Baccosense</a>
                @auth
                <div>
                    <ul class="hidden md:grid grid-cols-5 justify-center gap-6">
                        <li>
                            <a class="flex flex-col items-center {{ request()->is('produk*') ? 'text-blue-600' : 'text-gray-600 hover:text-blue-600' }}" href="{{ url('/produk') }}">
                                <i class="fas fa-box mb-1 text-lg"></i>
                                <span class="text-sm">Produk</span>
                            </a>
                        </li>
                        <li>
                            <a class="flex flex-col items-center {{ request()->is('penjualan*') ? 'text-blue-600' : 'text-gray-600 hover:text-blue-600' }}" href="{{ url('/penjualan') }}">
                                <i class="fas fa-shopping-cart mb-1 text-lg"></i>
                                <span class="text-sm">Penjualan</span>
                            </a>
                        </li>
                        <li>
                            <a class="flex flex-col items-center {{ request()->is('dashboard*') ? 'text-blue-600' : 'text-gray-600 hover:text-blue-600' }}" href="{{ url('/dashboard') }}">
                                <i class="fas fa-home mb-1 text-lg"></i>
                                <span class="text-sm">Home</span>
                            </a>
                        </li>
                        <li>
                            <a class="flex flex-col items-center {{ request()->is('pembelian*') ? 'text-blue-600' : 'text-gray-600 hover:text-blue-600' }}" href="{{ url('/pembelian') }}">
                                <i class="fas fa-shopping-bag mb-1 text-lg"></i>
                                <span class="text-sm">Pembelian</span>
                            </a>
                        </li>
                        <li>
                            <a class="flex flex-col items-center {{ request()->is('inventaris*') ? 'text-blue-600' : 'text-gray-600 hover:text-blue-600' }}" href="{{ url('/inventaris') }}">
                                <i class="fas fa-warehouse mb-1 text-lg"></i>
                                <span class="text-sm">Inventaris</span>
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="space-x-4">
                    <a class="flex flex-col items-center text-gray-600 hover:text-blue-600" href="{{ route('logout') }}">
                        <i class="fas fa-sign-out-alt mb-1 text-lg"></i>
                        <span class="hidden md:block text-sm">Logout</span>
                    </a>
                </div>
                @endauth
            </div>
        </div>
    </nav>