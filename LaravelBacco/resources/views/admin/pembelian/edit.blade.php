@extends('layouts.app')

@section('title', 'Edit Pembelian Produk')

@section('content')
    <main class="container mx-auto min-h-screen p-4 md:p-6">
        <div class="overflow-hidden rounded-lg bg-white shadow-md">
            <!-- Header Section -->
            <div class="flex flex-col items-start justify-between border-b border-gray-200 p-4 md:flex-row md:items-center">
                <h1 class="mb-2 text-xl font-semibold text-gray-800 md:mb-0">Edit Pembelian</h1>
                <a class="inline-flex items-center rounded-md bg-gray-200 px-4 py-2 text-sm text-gray-700 transition-colors hover:bg-gray-300"
                    href="{{ url('/pembelian') }}">
                    <i class="fas fa-arrow-left mr-2"></i> Kembali
                </a>
            </div>

            <!-- Form Section -->
            <div class="p-6">
                <form action="{{ route('pembelian.update', $pembelian->id) }}" method="POST">
                    @method('PUT')
                    @csrf
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <!-- Product Name -->
                        <div class="col-span-2">
                            <label class="mb-1 block text-sm font-medium text-gray-700" for="nama_produk">Nama
                                Produk</label>
                            <input
                                class="w-full rounded-md border border-gray-300 px-4 py-2 focus:border-blue-500 focus:ring-blue-500"
                                id="nama_produk" name="nama_produk" type="text"
                                value="{{ $pembelian->produk->nama_produk }}" placeholder="Masukkan nama produk" disabled>
                        </div>
                        <input name="produk_id" type="hidden" value="{{ $pembelian->produk_id }}">
                        <!-- Price -->
                        <div class="col-span-2 md:col-span-1">
                            <label class="mb-1 block text-sm font-medium text-gray-700" for="harga">Harga (Rp)</label>
                            <input
                                class="w-full rounded-md border border-gray-300 px-4 py-2 focus:border-blue-500 focus:ring-blue-500"
                                id="harga" name="harga" type="number" value="{{ $pembelian->harga }}"
                                placeholder="Masukkan harga" min="0" required>
                        </div>

                        <!-- Stock -->
                        <div class="col-span-2 md:col-span-1">
                            <label class="mb-1 block text-sm font-medium text-gray-700" for="stok">Jumlah </label>
                            <input
                                class="w-full rounded-md border border-gray-300 px-4 py-2 focus:border-blue-500 focus:ring-blue-500"
                                id="jumlah" name="jumlah" type="number" value="{{ $pembelian->jumlah }}"
                                placeholder="Masukkan jumlah pembelian" min="0" required>
                        </div>

                        {{-- Total Harga --}}
                        <div class="col-span-2 md:col-span-1">
                            <p class="mb-1 block text-sm font-medium text-gray-700">Total Harga (Rp)</p>
                            <p class="text-lg font-semibold text-gray-800" id="total_harga">Rp 0</p>
                        </div>

                    </div>

                    <!-- Validation Errors -->
                    @if ($errors->any())
                        <div class="mt-4 p-4 text-red-700" role="alert">
                            <ul class="list-inside list-disc">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Session Messages -->
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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
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
            jumlahInput.addEventListener('change', function() {
                const selectedOption = produkSelect.options[produkSelect.selectedIndex];
                const harga = selectedOption.getAttribute('data-harga');
                if (jumlahInput.value === '') {
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
