@extends('layouts.app')

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IoT Dashboard</title>
    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <!-- Font Awesome for Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <style>
        /* Animation styles */
        .gauge-container {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: #f3f4f6;
            overflow: hidden;
            opacity: 50%;
        }

        .gauge-fill {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            transition: width 0.5s ease, background-color 0.5s ease;
        }

        .temp-gauge {
            background-color: #ff6b6b;
        }

        .humidity-gauge {
            background-color: #4ecdc4;
        }

        /* Dynamic color classes */
        .cold {
            background-color: #93c5fd;
            /* Light blue */
        }

        .cool {
            background-color: #60a5fa;
            /* Blue */
        }

        .normal {
            background-color: #4ade80;
            /* Green */
        }

        .warm {
            background-color: #fbbf24;
            /* Yellow */
        }

        .hot {
            background-color: #f87171;
            /* Red */
        }

        .dry {
            background-color: #bfdbfe;
            /* Very light blue */
        }

        .comfortable {
            background-color: #86efac;
            /* Light green */
        }

        .moist {
            background-color: #5eead4;
            /* Teal */
        }

        .humid {
            background-color: #22d3ee;
            /* Cyan */
        }
    </style>
</head>

@section('content')
    <main class="container mx-auto min-h-screen p-4">
        <!-- Real-Time Data Section -->
        <div class="mb-8 grid grid-cols-1 gap-6 md:grid-cols-2">
            <!-- Temperature Card -->
            <div class="flex items-center space-x-4 rounded-lg bg-white p-6 shadow-lg relative">
                <div class="gauge-container z-0 rounded-lg">
                    <div class="gauge-fill temp-gauge" id="tempGauge"></div>
                </div>
                <i class="fas fa-thermometer-half text-4xl text-red-400 z-50"></i>
                <div class="w-full z-50">
                    <h2 class="text-md font-semibold md:text-xl">Suhu</h2>
                    <p class="text-2xl" id="temperature">22.5 °C</p>
                </div>
            </div>
            <!-- Humidity Card -->
            <div class="flex items-center space-x-4 rounded-lg bg-white p-6 shadow-lg relative">
                <div class="gauge-container rounded-lg z-0">
                    <div class="gauge-fill humidity-gauge" id="humidityGauge"></div>
                </div>
                <i class="z-50 fas fa-tint text-4xl text-blue-400"></i>
                <div class="w-full z-50">
                    <h2 class="text-md font-semibold md:text-xl">kelembapan</h2>
                    <p class="text-2xl" id="humidity">45 %</p>
                </div>
            </div>
        </div>

        <!-- Rest of your template remains the same -->
        <!-- Historical Data Chart -->
        <div class="mb-8 rounded-lg border border-gray-100 bg-white p-2 m shadow-lg md:p-6">
            <h2 class="text-md m-4 font-semibold lg:text-xl">History Suhu & Kelembapan</h2>
            <canvas id="historicalChart"></canvas>
        </div>

        <!-- Device Controls -->
        <h1 class="mb-4 text-center font-semibold" id="statusFuzzy">Status Auto Fuzzy : <span>Aktif</span></h1>
        <div class="grid grid-cols-2 gap-4 text-sm md:grid-cols-4">
            <!-- Humidifier Button -->
            <button
                class="flex items-center space-x-2 rounded-lg border border-gray-100 bg-white p-4 shadow-lg hover:scale-105"
                id="humidifierBtn" onclick="toggleDevice('humidifier')">
                <i class="fas fa-tint text-blue-400"></i>
                <span>Humidifier <span class="hidden" id="humidifierStatus">Off</span></span>
            </button>
            <!-- Dehumidifier Button -->
            <button
                class="flex items-center space-x-2 rounded-lg border border-gray-100 bg-white p-4 shadow-lg hover:scale-105"
                id="dehumidifierBtn" onclick="toggleDevice('dehumidifier')">
                <i class="fas fa-cloud text-blue-400"></i>
                <span>Dehumidifier <span class="hidden" id="dehumidifierStatus">Off</span></span>
            </button>
            <!-- Heater Button -->
            <button
                class="flex items-center space-x-2 rounded-lg border border-gray-100 bg-white p-4 shadow-lg hover:scale-105"
                id="heaterBtn" onclick="toggleDevice('heater')">
                <i class="fas fa-fire text-red-400"></i>
                <span>Heater <span class="hidden" id="heaterStatus">Off</span></span>
            </button>
            <!-- Fan Button -->
            <button
                class="flex items-center space-x-2 rounded-lg border border-gray-100 bg-white p-4 shadow-lg hover:scale-105"
                id="fanBtn" onclick="toggleDevice('fan')">
                <i class="fas fa-fan"></i>
                <span>Fan <span class="hidden" id="fanStatus">Off</span></span>
            </button>
        </div>
    </main>

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
            const fuzzyStatus = deviceStates.humidifier || deviceStates.dehumidifier || deviceStates.heater || deviceStates
                .fan ? 'Tidak Aktif' : 'Aktif';
            statusElement.textContent = deviceStates[device] ? 'On' : 'Off';
            buttonElement.classList.toggle('bg-white', !deviceStates[device]);
            buttonElement.classList.toggle('bg-green-300', deviceStates[device]);
            fuzzyStatusElement.textContent = fuzzyStatus;

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
                animation: {
                    duration: 500 // Disable animation for real-time updates
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

        // Simulate initial data
        const now = new Date();
        for (let i = 0; i < 20; i++) {
            now.setSeconds(now.getSeconds() + (5 * i)); // Add 5 seconds for each iteration
            const timeLabel = now.toLocaleTimeString();
            const temp = (Math.random() * 2 + 21).toFixed(1); // Random temp 21-23°C
            const humidity = (Math.random() * 10 + 40).toFixed(1); // Random humidity 40-50%

            historicalChart.data.labels.push(timeLabel);
            historicalChart.data.datasets[0].data.push(temp);
            historicalChart.data.datasets[1].data.push(humidity);
        }

        // Update gauge functions
        function updateTemperatureGauge(temp) {
            const gauge = document.getElementById('tempGauge');
            const percentage = Math.min(100, Math.max(0, (temp / 40) * 100)); // Scale to 0-40°C range

            // Update width with animation
            gauge.style.width = `${percentage}%`;

            // Update color based on temperature
            gauge.className = 'gauge-fill temp-gauge ' +
                (temp < 10 ? 'cold' :
                    temp < 18 ? 'cool' :
                    temp < 26 ? 'normal' :
                    temp < 32 ? 'warm' : 'hot');
        }

        function updateHumidityGauge(humidity) {
            const gauge = document.getElementById('humidityGauge');

            // Update width with animation
            gauge.style.width = `${humidity}%`;

            // Update color based on humidity
            gauge.className = 'gauge-fill humidity-gauge ' +
                (humidity < 30 ? 'dry' :
                    humidity < 50 ? 'comfortable' :
                    humidity < 70 ? 'moist' : 'humid');
        }

        // Initialize gauges
        updateTemperatureGauge(22.5);
        updateHumidityGauge(45);

        // Simulate real-time data updates
        const ws = new WebSocket('ws://localhost:6969');
        ws.onopen = function() {
            console.log('WebSocket connection established');
        };

        ws.onmessage = function(event) {
            const data = JSON.parse(event.data);
            const newTemp = data.data.suhu;
            const newHumidity = data.data.kelembapan;
            // Update real-time display
            updateRealTimeDisplay(newTemp, newHumidity);
        };

        ws.onclose = function(e) {
            console.log('Socket is closed. Reconnect will be attempted in 1 second.', e.reason);
            setTimeout(function() {
                connect();
            }, 1000);
        };

        ws.onerror = function(err) {
            console.error('Socket encountered error: ', err.message, 'Closing socket');
            ws.close();
        };

        function updateRealTimeDisplay(newTemp, newHumidity) {
            const now = new Date();
            const timeLabel = now.toLocaleTimeString();

            // Update real-time display
            document.getElementById('temperature').textContent = `${newTemp} °C`;
            document.getElementById('humidity').textContent = `${newHumidity} %`;

            // Update gauges
            updateTemperatureGauge(newTemp);
            updateHumidityGauge(newHumidity);

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
        }
    </script>
@endsection
