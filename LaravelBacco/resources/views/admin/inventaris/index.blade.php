@extends('layouts.app')

<head>
    <title>Data Inventaris</title>
    <!-- Font Awesome for Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
</head>

@section('content')
    <main class="max-w-screen container mx-auto min-h-screen p-0 md:p-6">
        <div class="overflow-hidden rounded-lg bg-white shadow-md">
            <div class="flex flex-col items-start justify-between border-b border-gray-200 p-4 md:flex-row md:items-center">
                <h1 class="mb-2 text-xl font-semibold text-gray-800 md:mb-0">Data Inventaris</h1>
                <div class="flex space-x-2">
                    <a class="inline-flex items-center rounded-md bg-green-600 px-4 py-2 text-sm text-white transition-colors hover:bg-green-700"
                        href="{{ route('inventaris.arima') }}">
                        <i class="fas fa-chart-line mr-2"></i> Prediksi ARIMA
                    </a>
                    <a class="inline-flex items-center rounded-md bg-blue-600 px-4 py-2 text-sm text-white transition-colors hover:bg-blue-700"
                        href="{{ route('produk.tambah') }}">
                        <i class="fas fa-plus mr-2"></i> Tambah Inventaris
                    </a>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="max-w-screen divide-y divide-gray-200 md:min-w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-700"
                                scope="col">Nama Produk</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-700"
                                scope="col">Jumlah</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-700"
                                scope="col">Rusak</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-700"
                                scope="col">Stok</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-700"
                                scope="col">Tanggal</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-700"
                                scope="col">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        @foreach ($inventaris as $inven)
                            <tr class="transition-colors hover:bg-gray-50 {{$inven->is_rusak ? 'bg-red-50 hover:bg-red-100' : 'bg-green-100 hover:bg-green-200'}}"> 
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-700">
                                    {{ $inven->produk->nama_produk }}</td>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-700">
                                    {{ $inven->jumlah }}</td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-700">
                                    {{ $inven->is_rusak ? 'Rusak' : '' }}</td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-700">{{ $inven->produk->stok }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-700">{{ $inven->created_at->format('d-m-Y') }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm font-medium">
                                    <div class="flex space-x-4">
                                        <a class="text-blue-600 hover:text-blue-900"
                                            href="{{ route('inventaris.edit', $inven->id) }}">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <form class="inline" action="{{ route('produk.destroy', $inven->id) }}"
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

            @if ($inventaris->isEmpty())
                <div class="p-6 text-center text-gray-700">
                    Tidak ada data Inventaris yang tersedia.
                </div>
            @endif

            <!-- Pagination would go here if needed -->
            <div class="border-t border-gray-200 px-6 py-4">
                {{ $inventaris->links() }}
            </div>
        </div>
    </main>
@endsection
