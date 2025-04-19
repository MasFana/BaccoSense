@extends('layouts.app')

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IoT Dashboard</title>
    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <!-- Font Awesome for Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
</head>

@section('content')
    <div class="container mx-auto p-6">
        <h1 class="mb-8 text-center text-3xl font-bold">IoT Dashboard</h1>

        <!-- Real-Time Data Section -->
        <div class="mb-8 grid grid-cols-1 gap-6 md:grid-cols-2">
            <!-- Temperature Card -->
            <div class="flex items-center space-x-4 rounded-lg border border-gray-400 bg-gray-200 p-6 shadow-lg">
                <i class="fas fa-thermometer-half text-4xl text-red-400"></i>
                <div>
                    <h2 class="text-xl font-semibold">Temperature</h2>
                    <p class="text-2xl" id="temperature">22.5 °C</p>
                </div>
            </div>
            <!-- Humidity Card -->
            <div class="flex items-center space-x-4 rounded-lg border border-gray-400 bg-gray-200 p-6 shadow-lg">
                <i class="fas fa-tint text-4xl text-blue-400"></i>
                <div>
                    <h2 class="text-xl font-semibold">Humidity</h2>
                    <p class="text-2xl" id="humidity">45 %</p>
                </div>
            </div>
        </div>

        <!-- Historical Data Chart -->
        <div class="mb-8 rounded-lg border border-gray-400 bg-gray-200 p-6 shadow-lg">
            <h2 class="mb-4 text-xl font-semibold">Historical Data</h2>
            <canvas id="historicalChart"></canvas>
        </div>

        <!-- Device Controls -->
        <h1 id="statusFuzzy" class="text-center mb-4">Status Auto Fuzzy : <span>Aktif</span></h1>
        <div class="grid grid-cols-2 gap-4 md:grid-cols-4 text-sm">
            <!-- Humidifier Button -->
            <button
                class="flex items-center space-x-4 rounded-lg border border-gray-400 bg-gray-200 p-4 shadow-lg hover:bg-gray-300"
                id="humidifierBtn" onclick="toggleDevice('humidifier')">
                <i class="fas fa-tint text-blue-400"></i>
                <span>Humidifier: <span id="humidifierStatus">Off</span></span>
            </button>
            <!-- Dehumidifier Button -->
            <button
                class="flex items-center space-x-4 rounded-lg border border-gray-400 bg-gray-200 p-4 shadow-lg hover:bg-gray-300"
                id="dehumidifierBtn" onclick="toggleDevice('dehumidifier')">
                <i class="fas fa-cloud text-blue-400"></i>
                <span>Dehumidifier: <span id="dehumidifierStatus">Off</span></span>
            </button>
            <!-- Heater Button -->
            <button
                class="flex items-center space-x-4 rounded-lg border border-gray-400 bg-gray-200 p-4 shadow-lg hover:bg-gray-300"
                id="heaterBtn" onclick="toggleDevice('heater')">
                <i class="fas fa-fire text-red-400"></i>
                <span>Heater: <span id="heaterStatus">Off</span></span>
            </button>
            <!-- Fan Button -->
            <button
                class="flex items-center space-x-4 rounded-lg border border-gray-400 bg-gray-200 p-4     shadow-lg hover:bg-gray-300"
                id="fanBtn" onclick="toggleDevice('fan')">
                <i class="fas fa-fan"></i>
                <span>Fan: <span id="fanStatus">Off</span></span>
            </button>
        </div>
    </div>

    <script>
        // Device state management
        const deviceStates = {
            humidifier: false,
            dehumidifier: false,
            heater: false,
            fan: false
        };

        // Toggle device state
        function toggleDevice(device) {
            deviceStates[device] = !deviceStates[device];
            const statusElement = document.getElementById(`${device}Status`);
            const buttonElement = document.getElementById(`${device}Btn`);
            const fuzzyStatusElement = document.getElementById('statusFuzzy').querySelector('span');
            const fuzzyStatus = deviceStates.humidifier || deviceStates.dehumidifier || deviceStates.heater || deviceStates.fan ? 'Aktif' : 'Tidak Aktif';
            statusElement.textContent = deviceStates[device] ? 'On' : 'Off';
            buttonElement.classList.toggle('bg-green-300', deviceStates[device]);

            // Simulate sending command to IoT device (e.g., via API or WebSocket)
            console.log(`${device} turned ${deviceStates[device] ? 'On' : 'Off'}`);
        }

        // Chart initialization
        const ctx = document.getElementById('historicalChart').getContext('2d');
        const historicalChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: [],
                datasets: [{
                        label: 'Temperature (°C)',
                        data: [],
                        borderColor: '#FF6B6B',
                        backgroundColor: 'rgba(255, 107, 107, 0.2)',
                        fill: true,
                        tension: 0.4
                    },
                    {
                        label: 'Humidity (%)',
                        data: [],
                        borderColor: '#4ECDC4',
                        backgroundColor: 'rgba(78, 205, 196, 0.2)',
                        fill: true,
                        tension: 0.4
                    }
                ]
            },
            options: {
                responsive: true,
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Time',
                            color: '#000000'
                        }
                    },
                    y: {
                        title: {
                            display: true,
                            text: 'Value',
                            color: '#000000'
                        },
                        beginAtZero: false
                    }
                },
                plugins: {
                    legend: {
                        labels: {
                            color: '#000000'
                        }
                    }
                }
            }
        });

        // Simulate real-time data updates
        setInterval(() => {
            const newTemp = (Math.random() * 2 + 21).toFixed(1); // Random temp 21-23°C
            const newHumidity = (Math.random() * 10 + 40).toFixed(1); // Random humidity 40-50%
            const now = new Date();
            const timeLabel = now.toLocaleTimeString();

            // Update real-time display
            document.getElementById('temperature').textContent = `${newTemp} °C`;
            document.getElementById('humidity').textContent = `${newHumidity} %`;

            // Update chart
            historicalChart.data.labels.push(timeLabel);
            historicalChart.data.datasets[0].data.push(newTemp);
            historicalChart.data.datasets[1].data.push(newHumidity);

            // Keep only last 20 data points
            if (historicalChart.data.labels.length > 20) {
                historicalChart.data.labels.shift();
                historicalChart.data.datasets[0].data.shift();
                historicalChart.data.datasets[1].data.shift();
            }

            historicalChart.update();
        }, 5000); // Update every 5 seconds
    </script>
@endsection
