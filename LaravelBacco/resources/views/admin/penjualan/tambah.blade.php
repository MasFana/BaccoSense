@extends('layouts.app')

@section('title', 'Tambah Penjualan Produk')

@section('content')
    <main class="container mx-auto min-h-screen p-4 md:p-6">
        <div class="overflow-hidden rounded-lg bg-white shadow-md">
            <!-- Header Section -->
            <div class="flex flex-col items-start justify-between border-b border-gray-200 p-4 md:flex-row md:items-center">
                <h1 class="mb-2 text-xl font-semibold text-gray-800 md:mb-0">Tambah Penjualan Baru</h1>
                <a class="inline-flex items-center rounded-md bg-gray-200 px-4 py-2 text-sm text-gray-700 transition-colors hover:bg-gray-300"
                    href="{{ route('penjualan.index') }}">
                    <i class="fas fa-arrow-left mr-2"></i> Kembali
                </a>
            </div>

            <!-- Form Section -->
            <div class="p-6">
                <form action="{{ route('penjualan.store') }}" method="POST">
                    @csrf
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <!-- Product Selection -->
                        <div class="col-span-2">
                            <label class="mb-1 block text-sm font-medium text-gray-700" for="produk_id">Produk</label>
                            <select
                                class="w-full rounded-md border border-gray-300 px-4 py-2 focus:border-blue-500 focus:ring-blue-500"
                                id="produk_id" name="produk_id" required>
                                <option value="" >Pilih Produk</option>
                                @foreach ($produk as $p)
                                    <option value="{{ $p->id }}" data-harga="{{ $p->harga }}">{{ $p->nama_produk }} (Stok: {{ $p->stok }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Quantity -->
                        <div class="col-span-2 md:col-span-1">
                            <label class="mb-1 block text-sm font-medium text-gray-700" for="jumlah">Jumlah</label>
                            <input
                                class="w-full rounded-md border border-gray-300 px-4 py-2 focus:border-blue-500 focus:ring-blue-500"
                                id="jumlah" name="jumlah" type="number" min="1" required>
                        </div>

                        <!-- Price -->
                        <div class="col-span-2 md:col-span-1">
                            <label class="mb-1 block text-sm font-medium text-gray-700" for="harga">Harga (Rp)</label>
                            <input
                                class="w-full rounded-md border border-gray-300 px-4 py-2 focus:border-blue-500 focus:ring-blue-500"
                                id="harga" name="harga" type="number" min="0" required>
                        </div>

                        {{-- Total Harga --}}
                        <div class="col-span-2 md:col-span-1">
                            <p class="mb-1 block text-sm font-medium text-gray-700">Total Harga (Rp)</p>
                            <p id="total_harga" class="font-semibold text-lg text-gray-800">Rp 0</p>
                        </div>

                        @if (session('error'))
                            <div class="col-span-2">
                                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative"
                                    role="alert">
                                    <strong class="font-bold">Error!</strong>
                                    <span class="block sm:inline">{{ session('error') }}</span>
                                </div>
                            </div>
                        @endif

                    </div>

                    <!-- Submit Button -->
                    <div class="mt-6 flex justify-end">
                        <button
                            class="flex items-center rounded-md bg-blue-600 px-6 py-2 text-white transition-colors hover:bg-blue-700"
                            type="submit">
                            <i class="fas fa-save mr-2"></i> Simpan Penjualan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>


        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const produkSelect = document.getElementById('produk_id');
                const jumlahInput = document.getElementById('jumlah');
                const hargaInput = document.getElementById('harga');
                const totalHargaElement = document.getElementById('total_harga');
        
                function updateTotalHarga() {
                    const jumlah = parseFloat(jumlahInput.value) || 0;
                    const harga = parseFloat(hargaInput.value) || 0;
                    const totalHarga = jumlah * harga;
                    totalHargaElement.textContent = 'Rp ' + totalHarga.toLocaleString('id-ID');
                }
        
                // Auto-set harga when product is selected
                produkSelect.addEventListener('change', function() {
                    const selectedOption = produkSelect.options[produkSelect.selectedIndex];
                    const harga = selectedOption.getAttribute('data-harga');
                    if(jumlahInput.value === '') {
                        jumlahInput.value = 1; // Set default quantity to 1 if not set
                    }
                    if (harga) {
                        hargaInput.value = harga;
                        updateTotalHarga();
                    }
                });
        
                jumlahInput.addEventListener('input', updateTotalHarga);
                hargaInput.addEventListener('input', updateTotalHarga);
        
                // Initialize total harga
                updateTotalHarga();
            });
        </script>
        

@endsection
