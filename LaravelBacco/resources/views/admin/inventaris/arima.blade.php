@extends('layouts.app')

@section('title', 'Baccosense Prediksi Arima')

@push('styles')
<style>
    .chart-container {
        position: relative;
        height: 300px;
        width: 100%;
    }
    .badge-improvement {
        @apply bg-yellow-100 text-yellow-800 text-xs font-medium px-2.5 py-0.5 rounded;
    }
    .badge-good {
        @apply bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded;
    }
    .badge-poor {
        @apply bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded;
    }
</style>
@endpush

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-xl md:text-3xl font-bold text-gray-800 mb-6">Baccosense Prediksi Arima</h1>
    
    <!-- Metadata Card -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-xl font-semibold text-gray-700">Data Prediksi</h2>
                <p class="text-gray-500">Dibuat pada {{ \Carbon\Carbon::parse($forecastData['date'])->format('M d, Y H:i') }}</p>
            </div>
            <span class="bg-blue-100 text-blue-800 text-sm font-medium px-3 py-1 rounded">
                {{ $forecastData['metadata']['model'] }} <span class="hidden md:block"></span>
            </span> 
        </div>
    </div>

    <!-- Main Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Metrics Summary -->
        <div class="bg-white rounded-lg shadow-md p-6 lg:col-span-1">
            <h2 class="text-xl font-semibold text-gray-700 mb-4">Metrik Performa</h2>
            
            <div class="space-y-4">
                @foreach($forecastData['metrics']['rmse'] as $product => $rmse)
                <div class="border-b border-gray-100 pb-4 last:border-0 last:pb-0">
                    <div class="flex justify-between items-start">
                        <h3 class="font-medium text-gray-800">{{ $product }}</h3>
                        <span class="@if($forecastData['metrics']['interpretation'][$product] == 'Good') badge-good @elseif($forecastData['metrics']['interpretation'][$product] == 'Needs improvement') badge-improvement @else badge-poor @endif">
                            {{ $forecastData['metrics']['interpretation'][$product] }}
                        </span>
                    </div>
                    <div class="grid grid-cols-2 gap-2 mt-2">
                        <div>
                            <p class="text-xs text-gray-500">RMSE</p>
                            <p class="font-medium">{{ number_format($rmse, 2) }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">RMSE %</p>
                            <p class="font-medium">{{ number_format($forecastData['metrics']['rmse_percentage'][$product], 1) }}%</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Avg Penjualan</p>
                            <p class="font-medium">{{ number_format($forecastData['metrics']['mean_sales'][$product], 0) }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Minat</p>
                            <p class="font-medium">
                                @if(in_array($product, $forecastData['trend_analysis']['increasing']))
                                <span class="text-green-600">↑ Meningkat</span>
                                @else
                                <span class="text-red-600">↓ Menurun</span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Charts Section -->
        <div class="bg-white rounded-lg shadow-md p-6 lg:col-span-2">
            <h2 class="text-xl font-semibold text-gray-700 mb-4">Prediksi Penjualan</h2>
            
            <div class="mb-4">
                <label for="product-select" class="block text-sm font-medium text-gray-700 mb-2">Pilih Produk</label>
                <select id="product-select" class="bg-gray-50 border border-gray-300 text-gray-700 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                    @foreach(array_keys($forecastData['forecasts']) as $product)
                    <option value="{{ $product }}">{{ $product }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="chart-container">
                <canvas id="salesChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Raw Data Section -->
    <div class="bg-white rounded-lg shadow-md p-6 mt-6">
        <h2 class="text-xl font-semibold text-gray-700 mb-4">Data Historis</h2>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sales</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($forecastData['original_data'] as $item)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ \Carbon\Carbon::parse($item['tanggal'])->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ $item['nama_produk'] }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $item['penjualan'] }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Prepare data for charts
    const forecastData = @json($forecastData);
    
    // Group historical data by product
    const historicalData = {};
    forecastData.original_data.forEach(item => {
        if (!historicalData[item.nama_produk]) {
            historicalData[item.nama_produk] = [];
        }
        historicalData[item.nama_produk].push({
            date: new Date(item.tanggal),
            sales: item.penjualan
        });
    });
    
    // Sort historical data by date
    Object.keys(historicalData).forEach(product => {
        historicalData[product].sort((a, b) => a.date - b.date);
    });
    
    // Initialize chart
    let salesChart;
    
    function renderChart(product) {
        const ctx = document.getElementById('salesChart').getContext('2d');
        
        // Prepare datasets
        const historyDates = historicalData[product].map(item => item.date);
        const historySales = historicalData[product].map(item => item.sales);
        
        const forecastDates = forecastData.forecasts[product].dates.map(date => new Date(date));
        const forecastSales = forecastData.forecasts[product].values;
        
        // Combined dates for x-axis
        const allDates = [...historyDates, ...forecastDates];
        
        if (salesChart) {
            salesChart.destroy();
        }
        
        salesChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: allDates.map(date => date.toLocaleDateString()),
                datasets: [
                    {
                        label: 'Penjualan Asli',
                        data: [...historySales, ...Array(forecastSales.length).fill(null)],
                        borderColor: 'rgb(75, 192, 192)',
                        backgroundColor: 'rgba(75, 192, 192, 0.1)',
                        tension: 0.3,
                        fill: true
                    },
                    {
                        label: 'Prediksi Penjualan',
                        data: [...Array(historySales.length).fill(null), ...forecastSales],
                        borderColor: 'rgb(255, 99, 132)',
                        backgroundColor: 'rgba(255, 99, 132, 0.1)',
                        borderDash: [5, 5],
                        tension: 0.3,
                        fill: true
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Date'
                        }
                    },
                    y: {
                        title: {
                            display: true,
                            text: 'Penjualan'
                        },
                        beginAtZero: false
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return `${context.dataset.label}: ${context.raw}`;
                            }
                        }
                    },
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: true,
                        text: `${product} Prediksi`,
                        font: {
                            size: 16
                        }
                    }
                }
            }
        });
    }
    
    // Initial render with first product
    renderChart(Object.keys(forecastData.forecasts)[0]);
    
    // Handle product selection change
    document.getElementById('product-select').addEventListener('change', function() {
        renderChart(this.value);
    });
</script>
@endpush