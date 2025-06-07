@extends('layouts.app')

@section('title', 'Tambah Inventaris')

@section('content')
    <main class="container mx-auto min-h-screen p-4 md:p-6">
        <div class="overflow-hidden rounded-lg bg-white shadow-md">
            <!-- Header Section -->
            <div class="flex flex-col items-start justify-between border-b border-gray-200 p-4 md:flex-row md:items-center">
                <h1 class="mb-2 text-xl font-semibold text-gray-800 md:mb-0">Tambah Inventaris Baru</h1>
                <a class="inline-flex items-center rounded-md bg-gray-200 px-4 py-2 text-sm text-gray-700 transition-colors hover:bg-gray-300"
                    href="{{ route('inventaris') }}">
                    <i class="fas fa-arrow-left mr-2"></i> Kembali
                </a>
            </div>

            <!-- Form Section -->
            <div class="p-6">
                <form action="{{ route('inventaris.create') }}" method="POST">
                    @csrf
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <!-- Product Selection -->
                        <div class="col-span-2">
                            <label class="mb-1 block text-sm font-medium text-gray-700" for="produk_id">Pilih Produk</label>
                            <select
                                class="w-full rounded-md border border-gray-300 px-4 py-2 focus:border-blue-500 focus:ring-blue-500"
                                id="produk_id" name="produk_id" required>
                                <option value="">Pilih Produk</option>
                                @foreach ($produks as $produk)
                                    <option value="{{ $produk->id }}">{{ $produk->nama_produk }} (Stok:
                                        {{ $produk->stok }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Quantity -->
                        <div class="col-span-2 md:col-span-1">
                            <label class="mb-1 block text-sm font-medium text-gray-700" for="jumlah">Jumlah</label>
                            <input
                                class="w-full rounded-md border border-gray-300 px-4 py-2 focus:border-blue-500 focus:ring-blue-500"
                                id="jumlah" name="jumlah" type="number" placeholder="Masukkan jumlah" min="0"
                                required>
                        </div>

                        <!-- Damaged Status -->
                        <div class="col-span-2 md:col-span-1">
                            <label class="mb-1 block text-sm font-medium text-gray-700" for="is_rusak">Status Barang</label>
                            <select
                                class="w-full rounded-md border border-gray-300 px-4 py-2 focus:border-blue-500 focus:ring-blue-500"
                                id="is_rusak" name="is_rusak" required>
                                <option value="0">Baik</option>
                                <option value="1">Rusak</option>
                            </select>
                        </div>
                    </div>

                    @if (session('error'))
                        <div class="col-span-2">
                            <div class="relative rounded border border-red-400 bg-red-100 px-4 py-3 text-red-700"
                                role="alert">
                                <strong class="font-bold">Error!</strong>
                                <span class="block sm:inline">{{ session('error') }}</span>
                            </div>
                        </div>
                    @endif
                    <!-- Submit Button -->
                    <div class="mt-6 flex justify-end">
                        <button
                            class="flex items-center rounded-md bg-blue-600 px-6 py-2 text-white transition-colors hover:bg-blue-700"
                            type="submit">
                            <i class="fas fa-save mr-2"></i> Simpan Inventaris
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>
@endsection
