@extends('layouts.app')
@section('title', 'Dashboard Baccosense')

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script src="https://unpkg.com/mqtt/dist/mqtt.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        /* Enhanced color scheme */
        :root {
            --primary: #4f46e5;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --info: #3b82f6;
        }

        .gauge-container {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            opacity: 0.7;
            border-radius: 1rem;
        }

        .gauge-fill {
            position: absolute;
            top: 0;
            left: 0;
            height: 100%;
            transition: all 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Enhanced temperature colors */
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

        .inset-depth {
            box-shadow: inset 0 4px 8px rgba(0, 0, 0, 0.1), inset 0 -4px 8px rgba(0, 0, 0, 0.05);
            background: linear-gradient(145deg, #f0f0f0, #e0e0e0);
        }

        .status-indicator {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            display: inline-block;
            margin-left: 5px;
        }

        .status-on {
            background-color: #4ade80;
            box-shadow: 0 0 8px #4ade80;
        }

        .status-off {
            background-color: #f87171;
        }

        .connection-status {
            position: fixed;
            top: 10px;
            right: 10px;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            z-index: 1000;
        }

        .connected {
            background-color: #4ade80;
            color: white;
        }

        .disconnected {
            background-color: #f87171;
            color: white;
        }

        .device-card {
            transition: all 0.3s ease;
        }

        .device-card.active {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        .auto-toggle {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
        }

        .toggle-switch {
            position: relative;
            display: inline-block;
            width: 60px;
            height: 34px;
        }

        .toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .toggle-slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .4s;
            border-radius: 34px;
        }

        .toggle-slider:before {
            position: absolute;
            content: "";
            height: 26px;
            width: 26px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }

        input:checked+.toggle-slider {
            background-color: #4ade80;
        }

        input:checked+.toggle-slider:before {
            transform: translateX(26px);
        }
    </style>
</head>

@section('content')
    <main class="container mx-auto min-h-screen p-4">
        <!-- Connection Status Indicator -->
        <div class="connection-status disconnected" id="connectionStatus">
            <i class="fas fa-plug"></i> Terputus
        </div>

        <!-- Real-Time Data Section -->
        <div class="mb-8 grid grid-cols-1 gap-6 md:grid-cols-2">
            <!-- Temperature Card -->
            <div class="relative flex items-center space-x-4 rounded-lg bg-gray-200 p-6">
                <div class="gauge-container inset-depth z-0 rounded-lg">
                    <div class="gauge-fill temp-gauge" id="tempGauge"></div>
                </div>
                <i class="fas fa-thermometer-half z-50 text-4xl text-red-400"></i>
                <div class="z-50 w-full">
                    <h2 class="text-md font-semibold md:text-xl">Suhu</h2>
                    <p class="text-2xl" id="temperature">-- °C</p>
                </div>
            </div>
            <!-- Humidity Card -->
            <div class="relative flex items-center space-x-4 rounded-lg bg-gray-200 p-6">
                <div class="gauge-container inset-depth z-0 rounded-lg">
                    <div class="gauge-fill humidity-gauge" id="humidityGauge"></div>
                </div>
                <i class="fas fa-tint z-50 text-4xl text-blue-400"></i>
                <div class="z-50 w-full">
                    <h2 class="text-md font-semibold md:text-xl">kelembapan</h2>
                    <p class="text-2xl" id="humidity">-- %</p>
                </div>
            </div>
        </div>

        <!-- Historical Data Chart -->
        <div class="m mb-8 rounded-lg border border-gray-100 bg-white p-2 shadow-lg md:p-6">
            <h2 class="text-md m-4 font-semibold lg:text-xl">History Suhu & Kelembapan</h2>
            <canvas id="historicalChart"></canvas>
        </div>

        <!-- Auto Mode Toggle -->
        <div class="auto-toggle">
            <div class="flex items-center space-x-4 rounded-lg bg-white p-4 shadow-lg">
                <span class="font-semibold">Auto Fuzzy Mode:</span>
                <label class="toggle-switch">
                    <input id="autoModeToggle" type="checkbox" checked>
                    <span class="toggle-slider"></span>
                </label>
                <span class="font-semibold" id="autoModeStatus">Aktif</span>
            </div>
        </div>

        <!-- Device Controls -->
        <div class="grid grid-cols-2 gap-4 text-sm md:grid-cols-4">
            <!-- Heater Button -->
            <button
                class="device-card flex items-center justify-between rounded-lg border border-gray-100 bg-white p-4 shadow-lg hover:scale-105"
                id="heaterBtn" data-device="heater" data-relay="1">
                <div class="flex items-center space-x-2">
                    <i class="fas fa-fire text-red-400"></i>
                    <span>Heater</span>
                </div>
                <span class="status-indicator status-off" id="heaterStatus"></span>
            </button>

            <!-- Cooler Button -->
            <button
                class="device-card flex items-center justify-between rounded-lg border border-gray-100 bg-white p-4 shadow-lg hover:scale-105"
                id="coolerBtn" data-device="cooler" data-relay="2">
                <div class="flex items-center space-x-2">
                    <i class="fas fa-snowflake text-blue-400"></i>
                    <span>Cooler</span>
                </div>
                <span class="status-indicator status-off" id="coolerStatus"></span>
            </button>

            <!-- Humidifier Button -->
            <button
                class="device-card flex items-center justify-between rounded-lg border border-gray-100 bg-white p-4 shadow-lg hover:scale-105"
                id="humidifierBtn" data-device="humidifier" data-relay="3">
                <div class="flex items-center space-x-2">
                    <i class="fas fa-tint text-blue-400"></i>
                    <span>Humidifier</span>
                </div>
                <span class="status-indicator status-off" id="humidifierStatus"></span>
            </button>

            <!-- Dehumidifier Button -->
            <button
                class="device-card flex items-center justify-between rounded-lg border border-gray-100 bg-white p-4 shadow-lg hover:scale-105"
                id="dehumidifierBtn" data-device="dehumidifier" data-relay="4">
                <div class="flex items-center space-x-2">
                    <i class="fas fa-wind text-blue-400"></i>
                    <span>Dehumidifier</span>
                </div>
                <span class="status-indicator status-off" id="dehumidifierStatus"></span>
            </button>
        </div>

        <!-- Additional Indicators -->
        <div class="mt-8 grid grid-cols-1 gap-4 md:grid-cols-2">
            <div class="rounded-lg border border-gray-100 bg-white p-4 shadow-lg">
                <h3 class="mb-2 font-semibold">System Status</h3>
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span>Fuzzy Logic:</span>
                        <span class="font-semibold" id="fuzzyStatus">Active</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Last Update:</span>
                        <span class="font-semibold" id="lastUpdate">--</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Distance Sensor:</span>
                        <span class="font-semibold" id="distanceValue">-- cm</span>
                    </div>
                </div>
            </div>
            <div class="rounded-lg border border-gray-100 bg-white p-4 shadow-lg">
                <h3 class="mb-2 font-semibold">Device States</h3>
                <div class="grid grid-cols-2 gap-2">
                    <div class="flex items-center">
                        <span class="status-indicator status-off mr-2" id="heaterStateIndicator"></span>
                        <span>Heater</span>
                    </div>
                    <div class="flex items-center">
                        <span class="status-indicator status-off mr-2" id="coolerStateIndicator"></span>
                        <span>Cooler</span>
                    </div>
                    <div class="flex items-center">
                        <span class="status-indicator status-off mr-2" id="humidifierStateIndicator"></span>
                        <span>Humidifier</span>
                    </div>
                    <div class="flex items-center">
                        <span class="status-indicator status-off mr-2" id="dehumidifierStateIndicator"></span>
                        <span>Dehumidifier</span>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
        // MQTT Configuration
        const MQTT_CONFIG = {
            host: '7d677df85f0a4b2ca584c9e9c7f3a1de.s1.eu.hivemq.cloud',
            port: 8884,
            username: 'fanaqt',
            password: 'Fana12345',
            clientId: 'webfana',
            publishTopic: 'r',
            subscribeTopic: 'sk'
        };

        // Device state management
        const deviceStates = {
            heater: false,
            cooler: false,
            humidifier: false,
            dehumidifier: false
        };

        let autoMode = true;
        let mqttClient = null;
        let lastUpdateTime = null;

        // Initialize MQTT connection
        function initMQTT() {
            const protocol = 'wss';
            const url = `${protocol}://${MQTT_CONFIG.host}:${MQTT_CONFIG.port}/mqtt`;

            const options = {
                username: MQTT_CONFIG.username,
                password: MQTT_CONFIG.password,
                clientId: MQTT_CONFIG.clientId,
                clean: true,
                reconnectPeriod: 1000,
                connectTimeout: 30 * 1000,
            };

            mqttClient = mqtt.connect(url, options);

            mqttClient.on('connect', () => {
                console.log('Connected to MQTT broker');
                updateConnectionStatus(true);
                mqttClient.subscribe(MQTT_CONFIG.subscribeTopic, (err) => {
                    if (!err) {
                        console.log(`Subscribed to ${MQTT_CONFIG.subscribeTopic}`);
                    }
                });
            });

            mqttClient.on('error', (err) => {
                console.error('MQTT error:', err);
                updateConnectionStatus(false);
            });

            mqttClient.on('close', () => {
                console.log('MQTT connection closed');
                updateConnectionStatus(false);
            });

            mqttClient.on('message', (topic, message) => {
                console.log(`Received message on ${topic}: ${message.toString()}`);
                processMQTTMessage(message.toString());
            });

            mqttClient.on('reconnect', () => {
                console.log('Attempting to reconnect to MQTT...');
            });

            mqttClient.on('offline', () => {
                console.log('MQTT client is offline');
                updateConnectionStatus(false);
            });
        }

        // Process incoming MQTT messages
        function processMQTTMessage(message) {
            try {
                const data = JSON.parse(message);
                console.log('Parsed data:', data);

                // Update sensor values
                if (data.s !== undefined) {
                    updateTemperature(data.s);
                }
                if (data.k !== undefined) {
                    updateHumidity(data.k);
                }
                if (data.j !== undefined) {
                    document.getElementById('distanceValue').textContent = `${data.j.toFixed(1)} cm`;
                }

                // Update relay states
                if (data.r1 !== undefined) {
                    updateDeviceState('heater', data.r1);
                }
                if (data.r2 !== undefined) {
                    updateDeviceState('cooler', data.r2);
                }
                if (data.r3 !== undefined) {
                    updateDeviceState('humidifier', data.r3);
                }
                if (data.r4 !== undefined) {
                    updateDeviceState('dehumidifier', data.r4);
                }

                // Update auto mode
                if (data.auto !== undefined) {
                    autoMode = data.auto === 1;
                    document.getElementById('autoModeToggle').checked = autoMode;
                    document.getElementById('autoModeStatus').textContent = autoMode ? 'Aktif' : 'Tidak Aktif';
                    document.getElementById('fuzzyStatus').textContent = autoMode ? 'Active' : 'Manual';
                }

                // Update last update time
                lastUpdateTime = new Date();
                document.getElementById('lastUpdate').textContent = lastUpdateTime.toLocaleTimeString();

            } catch (e) {
                console.error('Error processing MQTT message:', e);
            }
        }

        // Update connection status UI
        function updateConnectionStatus(connected) {
            const statusElement = document.getElementById('connectionStatus');
            if (connected) {
                statusElement.className = 'connection-status connected';
                statusElement.innerHTML = '<i class="fas fa-plug"></i> Terhubung';
            } else {
                statusElement.className = 'connection-status disconnected';
                statusElement.innerHTML = '<i class="fas fa-plug"></i> Terputus';
            }
        }

        // Update temperature display and gauge
        function updateTemperature(temp) {
            document.getElementById('temperature').textContent = `${temp.toFixed(1)} °C`;
            updateTemperatureGauge(temp);
        }

        // Update humidity display and gauge
        function updateHumidity(humidity) {
            document.getElementById('humidity').textContent = `${humidity.toFixed(1)} %`;
            updateHumidityGauge(humidity);
        }

        // Update device state and UI
        function updateDeviceState(device, state) {
            deviceStates[device] = state;
            const statusElement = document.getElementById(`${device}Status`);
            const stateIndicator = document.getElementById(`${device}StateIndicator`);
            const buttonElement = document.getElementById(`${device}Btn`);

            if (state) {
                statusElement.className = 'status-indicator status-on';
                stateIndicator.className = 'status-indicator status-on';
                buttonElement.classList.add('active');
                buttonElement.classList.add('bg-green-100');
            } else {
                statusElement.className = 'status-indicator status-off';
                stateIndicator.className = 'status-indicator status-off';
                buttonElement.classList.remove('active');
                buttonElement.classList.remove('bg-green-100');
            }
        }

        // Toggle device state and send MQTT command
        function toggleDevice(device) {
            if (autoMode) {
                alert('Fuzzy mode aktif, tidak dapat mengubah perangkat secara manual.');
                return;
            }

            const relayNum = document.getElementById(`${device}Btn`).getAttribute('data-relay');
            const newState = !deviceStates[device];

            const command = {
                r: parseInt(relayNum),
                s: newState ? 1 : 0
            };

            sendMQTTCommand(command);
        }

        // Toggle auto mode and send MQTT command
        function toggleAutoMode() {
            autoMode = !autoMode;
            const command = {
                auto: autoMode ? 1 : 0
            };

            document.getElementById('autoModeStatus').textContent = autoMode ? 'Aktif' : 'Tidak Aktif';
            document.getElementById('fuzzyStatus').textContent = autoMode ? 'Active' : 'Manual';

            sendMQTTCommand(command);
        }

        // Send command via MQTT
        function sendMQTTCommand(command) {
            if (mqttClient && mqttClient.connected) {
                mqttClient.publish(MQTT_CONFIG.publishTopic, JSON.stringify(command));
                console.log('Command sent:', command);
            } else {
                console.error('MQTT client not connected');
            }
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
                        tension: 0.4,
                        yAxisID: 'y'
                    },
                    {
                        label: 'Humidity (%)',
                        data: [],
                        borderColor: '#4ECDC4',
                        backgroundColor: 'rgba(78, 205, 196, 0.2)',
                        fill: true,
                        tension: 0.4,
                        yAxisID: 'y1'
                    }
                ]
            },
            options: {
                responsive: true,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Time',
                            color: '#000000'
                        }
                    },
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        title: {
                            display: true,
                            text: 'Temperature (°C)',
                            color: '#000000'
                        },
                        min: 0,
                        max: 80
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        title: {
                            display: true,
                            text: 'Humidity (%)',
                            color: '#000000'
                        },
                        min: 0,
                        max: 80,
                        grid: {
                            drawOnChartArea: false
                        }
                    }
                },
                plugins: {
                    legend: {
                        labels: {
                            color: '#000000'
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                label += context.parsed.y.toFixed(1);
                                if (context.datasetIndex === 0) {
                                    label += '°C';
                                } else {
                                    label += '%';
                                }
                                return label;
                            }
                        }
                    }
                }
            }
        });




        // Initialize the application
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize MQTT connection
            initMQTT();

            // Initialize chart with history data
            @if (isset($history))
                const historyData = @json($history);
                console.log('History data:', historyData);
                historyData.forEach(record => {
                    const time = new Date(record.created_at).toLocaleTimeString();
                    historicalChart.data.labels.push(time);
                    historicalChart.data.datasets[0].data.push(Number(record.suhu));
                    historicalChart.data.datasets[1].data.push(Number(record.kelembaban));
                });
                historicalChart.update();
            @endif
            // Set up event listeners for device buttons
            document.querySelectorAll('[data-device]').forEach(button => {
                button.addEventListener('click', function() {
                    const device = this.getAttribute('data-device');
                    toggleDevice(device);
                });
            });

            // Set up event listener for auto mode toggle
            document.getElementById('autoModeToggle').addEventListener('change', toggleAutoMode);

            // Initialize gauges with default values
            updateTemperatureGauge(0);
            updateHumidityGauge(0);

            // Request initial state
            if (mqttClient && mqttClient.connected) {
                mqttClient.publish(MQTT_CONFIG.publishTopic, 'r');
            }
        });

        // Handle beforeunload to clean up MQTT connection
        window.addEventListener('beforeunload', function() {
            if (mqttClient) {
                mqttClient.end();
            }
        });
    </script>
@endsection
