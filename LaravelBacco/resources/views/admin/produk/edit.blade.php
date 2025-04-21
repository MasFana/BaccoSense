@extends('layouts.app')

@section('title', 'Tambah Produk')

@section('content')
    <main class="container mx-auto min-h-screen p-4 md:p-6">
        <div class="overflow-hidden rounded-lg bg-white shadow-md">
            <!-- Header Section -->
            <div class="flex flex-col items-start justify-between border-b border-gray-200 p-4 md:flex-row md:items-center">
                <h1 class="mb-2 text-xl font-semibold text-gray-800 md:mb-0">Tambah Produk Baru</h1>
                <a class="inline-flex items-center rounded-md bg-gray-200 px-4 py-2 text-sm text-gray-700 transition-colors hover:bg-gray-300"
                    href="{{ url('/produk') }}">
                    <i class="fas fa-arrow-left mr-2"></i> Kembali
                </a>
            </div>

            <!-- Form Section -->
            <div class="p-6">
                <form action="{{ route('produk.update', $produk->id) }}" method="POST">
                    @csrf
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <!-- Product Name -->
                        <div class="col-span-2">
                            <label class="mb-1 block text-sm font-medium text-gray-700" for="nama_produk">Nama
                                Produk</label>
                            <input
                                class="w-full rounded-md border border-gray-300 px-4 py-2 focus:border-blue-500 focus:ring-blue-500"
                                id="nama_produk" name="nama_produk" type="text" value="{{ $produk->nama_produk }}"
                                placeholder="Masukkan nama produk" required>
                        </div>

                        <!-- Price -->
                        <div class="col-span-2 md:col-span-1">
                            <label class="mb-1 block text-sm font-medium text-gray-700" for="harga">Harga (Rp)</label>
                            <input
                                class="w-full rounded-md border border-gray-300 px-4 py-2 focus:border-blue-500 focus:ring-blue-500"
                                id="harga" name="harga" type="number" value="{{ $produk->harga }}"
                                placeholder="Masukkan harga" min="0" required>
                        </div>

                        <!-- Stock -->
                        <div class="col-span-2 md:col-span-1">
                            <label class="mb-1 block text-sm font-medium text-gray-700" for="stok">Stok</label>
                            <input
                                class="w-full rounded-md border border-gray-300 px-4 py-2 focus:border-blue-500 focus:ring-blue-500"
                                id="stok" name="stok" type="number" value="{{ $produk->stok }}"
                                placeholder="Masukkan jumlah stok" min="0" required>
                        </div>

                        <!-- Description -->
                        <div class="col-span-2">
                            <label class="mb-1 block text-sm font-medium text-gray-700" for="deskripsi">Deskripsi</label>
                            <textarea class="w-full rounded-md border border-gray-300 px-4 py-2 focus:border-blue-500 focus:ring-blue-500"
                                id="deskripsi" name="deskripsi" value="{{ $produk->deskripsi }}" rows="3"
                                placeholder="Masukkan deskripsi produk"></textarea>
                        </div>

                        <!-- Satuan -->
                        <div class="col-span-2">
                            <label class="mb-1 block text-sm font-medium text-gray-700" for="satuan">Satuan</label>
                            <input
                                class="w-full rounded-md border border-gray-300 px-4 py-2 focus:border-blue-500 focus:ring-blue-500"
                                id="satuan" name="satuan" value="{{ $produk->satuan }}"></input>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    @if (session('success'))
                        <div class="mb-4 border-l-4 border-green-500 bg-green-100 p-4 text-green-700" role="alert">
                            <p>{{ session('success') }}</p>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="mb-4 border-l-4 border-red-500 bg-red-100 p-4 text-red-700" role="alert">
                            <p>{{ session('error') }}</p>
                        </div>
                    @endif

                    <div class="mt-6 flex justify-end">
                        <button
                            class="flex items-center rounded-md bg-blue-600 px-6 py-2 text-white transition-colors hover:bg-blue-700"
                            type="submit" onclick="return confirm('Apakah Anda yakin ingin menyimpan perubahan ini?')">
                            <i class="fas fa-save mr-2"></i> Simpan Produk
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <!-- Include Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">

@endsection
