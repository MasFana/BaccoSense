@extends('layouts.app')

@section('title', 'Produk')

@section('content')
    <main class="max-w-screen container mx-auto min-h-screen p-0 md:p-6">
        <div class="overflow-hidden rounded-lg bg-white shadow-md">
            <div class="flex flex-col items-start justify-between border-b border-gray-200 p-4 md:flex-row md:items-center">
                <h1 class="mb-2 text-xl font-semibold text-gray-800 md:mb-0">Data Produk</h1>
                <a class="inline-flex items-center rounded-md bg-blue-600 px-4 py-2 text-sm text-white transition-colors hover:bg-blue-700"
                    href="{{ route('produk.tambah') }}">
                    <i class="fas fa-plus mr-2"></i> Tambah Produk
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="max-w-screen divide-y divide-gray-200 md:min-w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-700"
                                scope="col">Nama Produk</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-700"
                                scope="col">Harga</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-700"
                                scope="col">Satuan</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-700"
                                scope="col">Stok</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-700"
                                scope="col">Deskripsi</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-700"
                                scope="col">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        @foreach ($produks as $produk)
                            <tr class="transition-colors hover:bg-gray-50">
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-700">{{ $produk->nama_produk }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-700">Rp
                                    {{ number_format($produk->harga, 0, ',', '.') }}</td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-700">
                                    {{ $produk->satuan }}</td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-700">{{ $produk->stok }}</td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-700">{{ $produk->deskripsi }}</td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm font-medium">
                                    <div class="flex space-x-4">
                                        <a class="text-blue-600 hover:text-blue-900"
                                            href="{{ route('produk.edit', $produk->id) }}">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <!-- Validation Errors -->
                                        @if ($errors->any())
                                            alert({{$errors->first()}});
                                        @endif
                                        <form class="inline" action="{{ route('produk.destroy', $produk->id) }}"
                                            method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button class="text-red-600 hover:text-red-900" type="submit"
                                                onclick="return confirm('Apakah Anda yakin ingin menghapus produk ini?')">
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

            @if ($produks->isEmpty())
                <div class="p-6 text-center text-gray-700">
                    Tidak ada data produk yang tersedia.
                </div>
            @endif

            <!-- Pagination would go here if needed -->
            <div class="border-t border-gray-200 px-6 py-4">
                {{ $produks->links() }}
            </div>
        </div>
    </main>

@endsection
