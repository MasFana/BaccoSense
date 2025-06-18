@extends('layouts.app')

@section('title', 'Inventaris')

<head>
    <script src="https://unpkg.com/mqtt/dist/mqtt.min.js"></script>
    <style>
        .connection-status {
            position: fixed;
            bottom: 75px;
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
    </style>
</head>
@section('content')
    <main class="max-w-screen container mx-auto min-h-screen p-0 md:p-6">
        <div class="connection-status disconnected" id="connectionStatus">
            <i class="fas fa-plug"></i> Terputus
        </div>
        <div class="overflow-hidden rounded-lg bg-white shadow-md">
            <div class="flex flex-col items-start justify-between border-b border-gray-200 p-4 md:flex-row md:items-center">
                <h1 class="mb-2 text-xl font-semibold text-gray-800 md:mb-0">Data Inventaris</h1>
                <div class="flex space-x-2">
                    <a class="inline-flex items-center rounded-md bg-green-600 px-4 py-2 text-sm text-white transition-colors hover:bg-green-700"
                        href="{{ route('inventaris.arima') }}">
                        <i class="fas fa-chart-line mr-2"></i> Prediksi ARIMA
                    </a>
                    <a class="inline-flex items-center rounded-md bg-blue-600 px-4 py-2 text-sm text-white transition-colors hover:bg-blue-700"
                        href="{{ route('inventaris.tambah') }}">
                        <i class="fas fa-plus mr-2"></i> Tambah Inventaris
                    </a>
                </div>
            </div>

            <div class="overflow-x-auto">
                <x-capacity-penyimpanan id="storageCapacity" :used="0" :total="0" />
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
                                scope="col">Tanggal Dibuat</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-700"
                                scope="col">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        @foreach ($inventaris as $inven)
                            <tr
                                class="{{ $inven->is_rusak ? 'bg-red-50 hover:bg-red-100' : 'bg-green-100 hover:bg-green-200' }} transition-colors hover:bg-gray-50">
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-700">
                                    {{ $inven->produk->nama_produk }}</td>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-700">
                                    {{ $inven->jumlah }}</td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-700">
                                    {{ $inven->is_rusak ? 'Rusak' : '' }}</td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-700">{{ $inven->produk->stok }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-700">
                                    {{ $inven->created_at->format('d M Y H:i:s') }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm font-medium">
                                    <div class="flex space-x-4">
                                        @if ($inven->is_rusak)
                                            <span class="text-red-600">Rusak</span>
                                        @else
                                            <a class="text-blue-600 hover:text-blue-900"
                                                href="{{ route('inventaris.edit', $inven->id) }}">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                            <form class="inline" action="{{ route('inventaris.destroy', $inven->id) }}"
                                                method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button class="inline-flex items-center text-green-600 hover:text-green-900"
                                                    type="submit"
                                                    onclick="return confirm('Apakah Anda yakin ingin mengembalikan produk ini ke stok?')">
                                                    <i class="fas fa-undo-alt mr-1"></i> Kembalikan ke Stok
                                                </button>
                                            </form>
                                        @endif

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

        let mqttClient = null;
        let lastUpdateTime = null;

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

                if (data.j !== undefined && data.j > 0) {
                    console.log('Updating storage capacity:', data.j);
                    updateCapacitySlider('storageCapacity', data.j);
                }

            } catch (e) {
                console.error('Error processing MQTT message:', e);
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            initMQTT();
        });

        // Handle beforeunload to clean up MQTT connection
        window.addEventListener('beforeunload', function() {
            if (mqttClient) {
                mqttClient.end();
            }
        });
    </script>
@endsection
