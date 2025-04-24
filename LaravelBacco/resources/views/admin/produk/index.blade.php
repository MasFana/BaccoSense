@extends('layouts.app')

@section('content')
<main class="container mx-auto p-0 md:p-6 min-h-screen max-w-screen">
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center p-4 border-b border-gray-200">
            <h1 class="text-xl font-semibold text-gray-800 mb-2 md:mb-0">Data Produk</h1>
            <a href="{{route('produk.tambah')}}" class="inline-flex text-sm items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                <i class="fas fa-plus mr-2"></i> Tambah Produk
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="max-w-screen md:min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Nama Produk</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Harga</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Satuan</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Stok</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Deskripsi</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($produks as $produk)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $produk->nama_produk }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">Rp {{ number_format($produk->harga, 0, ',', '.') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-700">{{ $produk->satuan }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $produk->stok }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $produk->deskripsi }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-4">
                                <a href="{{ route('produk.edit', $produk->id) }}" class="text-blue-600 hover:text-blue-900">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <form action="{{ route('produk.destroy',$produk->id) }}" method="POST" class="inline"> 
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Apakah Anda yakin ingin menghapus produk ini?')">
                                        <i class="fas fa-trash-alt"></i> Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($produks->isEmpty())
        <div class="p-6 text-center text-gray-700">
            Tidak ada data produk yang tersedia.
        </div>
        @endif

        <!-- Pagination would go here if needed -->
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $produks->links() }}
        </div>
    </div>
</main>

@endsection