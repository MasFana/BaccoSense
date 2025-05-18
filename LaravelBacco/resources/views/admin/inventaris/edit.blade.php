@extends('layouts.app')

@section('title', 'Edit Inventaris')

@section('content')
    <main class="container mx-auto min-h-screen p-4 md:p-6">
        <div class="overflow-hidden rounded-lg bg-white shadow-md">
            <!-- Header Section -->
            <div class="flex flex-col items-start justify-between border-b border-gray-200 p-4 md:flex-row md:items-center">
                <h1 class="mb-2 text-xl font-semibold text-gray-800 md:mb-0">Edit Inventaris</h1>
                <a class="inline-flex items-center rounded-md bg-gray-200 px-4 py-2 text-sm text-gray-700 transition-colors hover:bg-gray-300"
                    href="{{ route('inventaris') }}">
                    <i class="fas fa-arrow-left mr-2"></i> Kembali
                </a>
            </div>

            <!-- Form Section -->
            <div class="p-6">
                <form action="{{ route('inventaris.update', $inventaris->id) }}" method="POST">
                    @csrf
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <!-- Product Name -->
                        <div class="col-span-2">
                            <label class="mb-1 block text-sm font-medium text-gray-700" for="nama_produk">Nama
                                Produk</label>
                            <select
                                class="w-full rounded-md border border-gray-300 px-4 py-2 focus:border-blue-500 focus:ring-blue-500"
                                id="produk_id" name="produk_id" required onchange="updateStockLimit(this)">
                                <option value="">Pilih Produk</option>
                                @foreach($produks as $produk)
                                    <option value="{{ $produk->id }}" 
                                        data-stock="{{ $produk->stok }}"
                                        {{ $inventaris->produk_id == $produk->id ? 'selected' : '' }}>
                                        {{ $produk->nama_produk }} (Stok: {{ $produk->stok }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Jumlah -->
                        <div class="col-span-2 md:col-span-1">
                            <label class="mb-1 block text-sm font-medium text-gray-700" for="jumlah">Jumlah</label>
                            <input
                                class="w-full rounded-md border border-gray-300 px-4 py-2 focus:border-blue-500 focus:ring-blue-500"
                                id="jumlah" name="jumlah" type="number" value="{{ $inventaris->jumlah }}"
                                placeholder="Masukkan jumlah" min="0" required>
                            <p id="stock-warning" class="mt-1 text-sm text-red-600 hidden">Jumlah melebihi stok yang tersedia!</p>
                        </div>

                        <!-- Is Rusak -->
                        <div class="col-span-2 md:col-span-1">
                            <label class="mb-1 block text-sm font-medium text-gray-700" for="is_rusak">Status</label>
                            <select
                                class="w-full rounded-md border border-gray-300 px-4 py-2 focus:border-blue-500 focus:ring-blue-500"
                                id="is_rusak" name="is_rusak" required>
                                <option value="0" {{ $inventaris->is_rusak == 0 ? 'selected' : '' }}>Baik</option>
                                <option value="1" {{ $inventaris->is_rusak == 1 ? 'selected' : '' }}>Rusak</option>
                            </select>
                        </div>
                    </div>

                    <!-- Alert Messages -->
                    <!-- Validation Errors -->
                    @if ($errors->any())
                        <div class="mt-4 p-4 text-red-700" role="alert">
                            <ul class="list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if (session('success'))
                        <div class="mb-4 mt-6 border-l-4 border-green-500 bg-green-100 p-4 text-green-700" role="alert">
                            <p>{{ session('success') }}</p>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="mb-4 mt-6 border-l-4 border-red-500 bg-red-100 p-4 text-red-700" role="alert">
                            <p>{{ session('error') }}</p>
                        </div>
                    @endif

                    <!-- Created At and Updated At Info -->
                    <div class="mt-6 rounded-md bg-gray-50 p-4">
                        <div class="grid grid-cols-1 gap-2 text-sm text-gray-600 md:grid-cols-2">
                            <p><i class="fas fa-clock mr-2"></i><strong>Dibuat:</strong> {{ $inventaris->created_at->format('d M Y H:i:s') }}</p>
                            <p><i class="fas fa-history mr-2"></i><strong>Diupdate:</strong> {{ $inventaris->updated_at->format('d M Y H:i:s') }}</p>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="mt-6 flex justify-end">
                        <button id="submit-btn"
                            class="flex items-center rounded-md bg-blue-600 px-6 py-2 text-white transition-colors hover:bg-blue-700"
                            type="submit" onclick="return validateForm()">
                            <i class="fas fa-save mr-2"></i> Simpan Inventaris
                        </button>
                    </div>
                </form>
            </div>

            <script>
                function updateStockLimit(selectElement) {
                    const selectedOption = selectElement.options[selectElement.selectedIndex];
                    const stockLimit = selectedOption.dataset.stock;
                    const jumlahInput = document.getElementById('jumlah');
                    
                    jumlahInput.max = stockLimit;
                    validateQuantity(jumlahInput.value, stockLimit);
                }

                function validateQuantity(value, max) {
                    const warning = document.getElementById('stock-warning');
                    const submitBtn = document.getElementById('submit-btn');
                    
                    if (parseInt(value) > parseInt(max)) {
                        warning.classList.remove('hidden');
                        submitBtn.disabled = true;
                        submitBtn.classList.add('opacity-50');
                    } else {
                        warning.classList.add('hidden');
                        submitBtn.disabled = false;
                        submitBtn.classList.remove('opacity-50');
                    }
                }

                function validateForm() {
                    const produkSelect = document.getElementById('produk_id');
                    const selectedOption = produkSelect.options[produkSelect.selectedIndex];
                    const stockLimit = selectedOption.dataset.stock;
                    const jumlahInput = document.getElementById('jumlah');

                    if (parseInt(jumlahInput.value) > parseInt(stockLimit)) {
                        alert('Jumlah tidak boleh melebihi stok yang tersedia!');
                        return false;
                    }
                    return confirm('Apakah Anda yakin ingin menyimpan perubahan ini?');
                }

                // Add event listener to quantity input
                document.getElementById('jumlah').addEventListener('input', function() {
                    const produkSelect = document.getElementById('produk_id');
                    const selectedOption = produkSelect.options[produkSelect.selectedIndex];
                    const stockLimit = selectedOption.dataset.stock;
                    validateQuantity(this.value, stockLimit);
                });

                // Initialize on page load
                window.onload = function() {
                    const produkSelect = document.getElementById('produk_id');
                    updateStockLimit(produkSelect);
                }
            </script>
        </div>
    </main>

    <!-- Include Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    

@endsection
