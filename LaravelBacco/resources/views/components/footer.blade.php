    @props(['active'])

    <footer class="sticky bottom-0 z-10 w-full bg-white py-3 shadow-md border-t-[0.1px] border-gray-200 md:hidden">
        <div class="container mx-auto">
            <ul class="flex items-center justify-around">
                <li>
                    <a class="{{ request()->is('produk*') ? 'text-blue-600 border-blue-600' : 'text-gray-600' }} flex flex-col items-center hover:text-blue-600"
                        href="{{ url('/produk') }}">
                        <i class="fas fa-box mb-1 text-lg"></i>
                        <span class="text-xs">Produk</span>
                    </a>
                </li>
                <li>
                    <a class="{{ request()->is('penjualan*') ? 'text-blue-600 border-blue-600' : 'text-gray-600' }} flex flex-col items-center hover:text-blue-600"
                        href="{{ url('/penjualan') }}">
                        <i class="fas fa-shopping-cart mb-1 text-lg"></i>
                        <span class="text-xs">Penjualan</span>
                    </a>
                </li>
                <li>
                    <a class="{{ request()->is('dashboard*') ? 'text-blue-600 border-blue-600' : 'text-gray-600' }} flex flex-col items-center hover:text-blue-600"
                        href="{{ url('/dashboard') }}">
                        <i class="fas fa-home mb-1 text-lg"></i>
                        <span class="text-xs">Home</span>
                    </a>
                </li>
                <li>
                    <a class="{{ request()->is('pembelian*') ? 'text-blue-600 border-blue-600' : 'text-gray-600' }} flex flex-col items-center hover:text-blue-600"
                        href="{{ url('/pembelian') }}">
                        <i class="fas fa-shopping-bag mb-1 text-lg"></i>
                        <span class="text-xs">Pembelian</span>
                    </a>
                </li>
                <li>
                    <a class="{{ request()->is('inventaris*') ? 'text-blue-600 border-blue-600' : 'text-gray-600' }} flex flex-col items-center hover:text-blue-600"
                        href="{{ url('/inventaris') }}">
                        <i class="fas fa-warehouse mb-1 text-lg"></i>
                        <span class="text-xs">Inventaris</span>
                    </a>
                </li>
            </ul>
        </div>
    </footer>
