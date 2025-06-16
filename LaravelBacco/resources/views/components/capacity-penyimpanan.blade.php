@props(['used' => 0, 'total' => 100, 'id' => 'capacitySlider'])

@php
    $percentage = $total > 0 ? min(100, round(($used / $total) * 100)) : 0;
    $color = 'bg-green-500';
    if ($percentage > 90) {
        $color = 'bg-red-500';
    } elseif ($percentage > 60) {
        $color = 'bg-yellow-500';
    }
@endphp

<div class="mx-auto min-h-fit w-full rounded-md bg-white p-4 shadow" id="{{ $id }}">
    <div class="mb-2 flex items-center justify-between">
        <span class="text-sm font-medium text-gray-700">Status Penyimpanan</span>
        <span class="text-sm font-semibold text-gray-800" id="{{ $id }}-percentage">
            {{ $percentage }}%
        </span>
    </div>
    <div class="h-8 w-full overflow-hidden rounded-md bg-gray-200">
        <div class="{{ $color }} h-full transition-all duration-500 ease-in-out" id="{{ $id }}-bar"
            style="width: {{ $percentage }}%">
        </div>
    </div>
    <div class="flex items-center justify-between">
        <div class="mt-2 text-xs text-gray-500" id="{{ $id }}-label">
            {{ number_format($used) }} / {{ number_format($total) }}
        </div>
        <button
        class="mt-4 rounded bg-blue-500 px-3 py-1 text-sm text-white hover:bg-blue-600"
        onclick="resetJarakKosong('{{ $id }}')">
        Reset Jarak Kosong
    </button>
</div>
</div>

@once
    @push('scripts')
        <script>
            // Store state globally (could be scoped in a real app)
            const jarakState = {
                total: {{ $total }},
                used: {{ $used }},
                id: '{{ $id }}',
            };

            function updateCapacitySlider(id, used, total = jarakState.total) {
                const bar = document.getElementById(`${id}-bar`);
                const percentageLabel = document.getElementById(`${id}-percentage`);
                const numberLabel = document.getElementById(`${id}-label`);

                const percentage = total > 0 ? Math.min(100, Math.round((used / total) * 100)) : 0;

                jarakState.used = used;

                bar.style.width = percentage + '%';
                percentageLabel.textContent = percentage + '%';
                numberLabel.textContent = `${used} / ${total}`;

                bar.classList.remove('bg-blue-500', 'bg-yellow-500', 'bg-red-500', 'bg-green-500');
                if (percentage > 90) {
                    bar.classList.add('bg-red-500');
                } else if (percentage > 60) {
                    bar.classList.add('bg-yellow-500');
                } else {
                    bar.classList.add('bg-green-500');
                }
            }

            function initStateKosong(id) {
                fetch('/jarak-kosong')
                    .then(response => response.json())
                    .then(data => {
                        const total = data.jarak_kosong;
                        jarakState.total = total;
                        updateCapacitySlider(id, jarakState.used, total);
                        console.log('Jarak kosong initialized:', total);
                    })
                    .catch(error => console.error('Error fetching jarak kosong:', error));
            }

            function resetJarakKosong(id) {
                fetch('/jarak-kosong', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            // 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            jarak_kosong: jarakState.used
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        updateCapacitySlider(id, jarakState.used, data.jarak_kosong);
                        jarakState.total = data.jarak_kosong;
                        console.log('Jarak kosong reset to:', data.jarak_kosong);
                    })
                    .catch(error => console.error('Error updating jarak kosong:', error));
            }

            // On DOM ready
            document.addEventListener('DOMContentLoaded', () => {
                initStateKosong(jarakState.id);
            });
        </script>
    @endpush
@endonce
