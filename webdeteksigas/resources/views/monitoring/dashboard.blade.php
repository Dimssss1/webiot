<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <title>Dashboard Deteksi Gas & Api</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="bg-gray-50 text-gray-800">
    <!-- Header -->
    <header class="bg-red-600 text-white shadow">
        <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">
            <h1 class="text-2xl font-bold">🔥 Sistem Monitoring Gas</h1>
            <span class="text-sm">Real-time IoT Dashboard</span>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-6 py-8">
        <!-- Info Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div class="bg-white p-6 rounded-xl shadow hover:shadow-md transition">
                <h2 class="text-lg font-semibold mb-1">Status Gas Saat Ini</h2>
                <div id="status-gas" class="text-4xl font-extrabold text-red-600">
                    {{ $latest?->gas_level ?? 'Tidak ada data' }}
                </div>
                <p class="text-gray-500 text-sm mt-2" id="last-updated">
                    Diperbarui: {{ $latest?->created_at ? $latest->created_at->format('d M Y, H:i:s') : '-' }}
                </p>
            </div>
            <div class="bg-white p-6 rounded-xl shadow hover:shadow-md transition">
                <h2 class="text-lg font-semibold mb-2">Ambang Batas & Kontrol</h2>
                <p class="mb-2">Threshold: <strong id="threshold-value">{{ $threshold ?? '300' }}</strong></p>
                <div class="flex flex-wrap gap-3 mt-4">
                    <button id="reset-btn" class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded">🔁 Reset</button>
                    <button id="buzzer-toggle" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">🔊 Toggle Buzzer</button>
                    <button id="led-toggle" class="bg-purple-500 hover:bg-purple-600 text-white px-4 py-2 rounded">💡 Toggle LED</button>
                </div>
            </div>
        </div>

        <!-- Status Perangkat -->
        <div class="grid grid-cols-2 gap-6 mb-8">
            <div class="bg-white p-6 rounded-xl shadow text-center">
                <p class="font-semibold text-gray-700">Status Buzzer</p>
                <div id="status-buzzer" class="text-3xl mt-3">🔈 Nonaktif</div>
            </div>
            <div class="bg-white p-6 rounded-xl shadow text-center">
                <p class="font-semibold text-gray-700">Status LED</p>
                <div id="status-led" class="text-3xl mt-3">💡 Mati</div>
            </div>
        </div>

        <!-- Tabel Riwayat -->
        <div class="bg-white p-6 rounded-xl shadow mb-8">
            <h2 class="text-lg font-semibold mb-4">Tabel Riwayat Level Gas</h2>
            <div class="overflow-x-auto">
                <table class="w-full text-left border border-gray-200 rounded">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="p-2 border">#</th>
                            <th class="p-2 border">Level Gas</th>
                            <th class="p-2 border">Waktu</th>
                        </tr>
                    </thead>
                    <tbody id="table-data-level-gas">
                        @foreach($history as $i => $row)
                        <tr class="hover:bg-gray-50">
                            <td class="p-2 border">{{ $i + 1 }}</td>
                            <td class="p-2 border">{{ $row->gas_level }}</td>
                            <td class="p-2 border">{{ $row->created_at->format('d M Y, H:i:s') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-gray-100 text-center text-sm text-gray-500 py-4 mt-6">
        &copy; {{ date('Y') }} Sistem Monitoring Gas Berbasis IoT.
    </footer>

    <!-- Script -->
    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        async function fetchLatestData() {
            try {
                const res = await fetch('/api/sensors/latest');
                const data = await res.json();

                document.getElementById('status-gas').innerText = data.gas_level;
                document.getElementById('status-buzzer').innerText = data.buzzer ? '🔊 Aktif' : '🔈 Nonaktif';
                document.getElementById('status-led').innerText = data.led ? '💡 Nyala' : '💡 Mati';
                document.getElementById('last-updated').innerText = "Diperbarui: " + new Date(data.created_at).toLocaleString('id-ID');

                // Tambah data ke tabel jika belum ada
                const tbody = document.getElementById('table-data-level-gas');
                const newTime = new Date(data.created_at).toLocaleString('id-ID');
                const existingRows = [...tbody.querySelectorAll('tr')];
                const isDuplicate = existingRows.some(row => row.cells[2].innerText === newTime);

                if (!isDuplicate) {
                    const newRow = document.createElement('tr');
                    newRow.className = 'hover:bg-gray-50';
                    newRow.innerHTML = `
                        <td class="p-2 border">${existingRows.length + 1}</td>
                        <td class="p-2 border">${data.gas_level}</td>
                        <td class="p-2 border">${newTime}</td>
                    `;
                    tbody.prepend(newRow); // Tambah di atas
                    // Hapus jika lebih dari 20 baris
                    if (tbody.children.length > 20) {
                        tbody.removeChild(tbody.lastChild);
                    }
                }
            } catch (err) {
                console.error('Gagal ambil data terbaru:', err);
            }
        }

        async function controlDevice(endpoint) {
            try {
                const res = await fetch(endpoint, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Content-Type': 'application/json'
                    }
                });
                if (!res.ok) throw new Error('Gagal mengirim perintah');
                showToast("✅ Perintah berhasil dikirim");
            } catch (err) {
                showToast("❌ Gagal mengirim: " + err.message);
            }
        }

        function showToast(message) {
            const toast = document.createElement('div');
            toast.innerText = message;
            toast.className = "fixed bottom-5 right-5 bg-gray-800 text-white px-4 py-2 rounded shadow-lg z-50 animate-bounce";
            document.body.appendChild(toast);
            setTimeout(() => toast.remove(), 3000);
        }

        // Event Listener
        document.getElementById('reset-btn').addEventListener('click', () => controlDevice('/api/reset'));
        document.getElementById('buzzer-toggle').addEventListener('click', () => controlDevice('/api/device/control/buzzer'));
        document.getElementById('led-toggle').addEventListener('click', () => controlDevice('/api/device/control/led'));

        // Auto refresh data setiap 5 detik
        setInterval(fetchLatestData, 5000);
    </script>
</body>
</html>
