# BaccoSense

**BaccoSense** adalah platform monitoring dan manajemen inventaris berbasis web yang terintegrasi dengan teknologi **IoT**, **AI (ARIMA Forecasting)**, dan dikembangkan menggunakan framework **Laravel** untuk frontend/backend serta **Python Flask** untuk machine learning & komunikasi IoT.

---

## 🧠 Fitur Utama

- **Prediksi Stok ARIMA:** Menggunakan model time series ARIMA untuk memprediksi kebutuhan stok ke depan.
- **Sensor IoT Realtime:** Integrasi dengan sensor suhu, kelembaban, dan jarak berbasis ESP8266 via MQTT.
- **Dashboard Inventaris:** UI manajemen produk, pembelian, penjualan, dan kondisi penyimpanan (termasuk status rusak/kosong).
- **WebSocket & MQTT Bridge:** Komunikasi realtime antara ESP8266, server Python, dan Laravel.
- **Manajemen Otomatis:** Sistem fuzzy logic mengontrol relay berdasarkan kondisi suhu dan kelembaban.

---

## 🛠️ Teknologi yang Digunakan

### Backend Web
- Laravel 10+
- MySQL / SQLite
- RESTful API
- Sanctum Authentication

### Backend AI & IoT
- Python 3.11+
- Flask + WebSocket (for real-time data)
- MQTT (Paho MQTT Client)
- ARIMA (statsmodels)

### IoT Devices
- ESP8266
- DHT11/DHT22
- Ultrasonic sensor
- Relay 4 channel

---

## 📁 Struktur Proyek

```

BaccoSense/
├── LaravelBacco/       # Aplikasi Laravel (web dan API)
├── FlaskArima/         # Server Python untuk IoT dan ARIMA Forecast
├── codeIOT.ino         # Kode Arduino untuk ESP8266

````

---

## 🚀 Cara Menjalankan

### 1. Jalankan Laravel

```bash
cd LaravelBacco
cp .env.example .env
composer install
php artisan key:generate
php artisan migrate --seed
php artisan serve
````

### 2. Jalankan Python Flask (IoT & Forecasting)

```bash
cd FlaskArima
python -m venv venv
source venv/bin/activate  # Windows: venv\Scripts\activate
pip install -r requirements.txt
python start.py
```

> Server akan menjalankan 2 proses paralel: `signaling.py` (IoT bridge via MQTT) dan `arima.py` (model forecasting).

### 3. Jalankan ESP8266

Upload `codeIOT.ino` ke board menggunakan Arduino IDE.

---

## 🌐 API Penting

* `POST /api/add` - Menambahkan data suhu & kelembaban dari MQTT
* `GET /api/jarak_kosong` - Status jarak kosong penyimpanan
* `GET /forecast` (Flask) - Endpoint ARIMA untuk Laravel

---

## 📊 Visualisasi

Dashboard Laravel menyediakan:

* Grafik suhu/kelembaban historis
* Forecast stok masa depan
* Status perangkat (relay dan auto/manual)
* Manajemen produk dan inventaris

---

## 🔐 Konfigurasi Penting

Di `.env` Laravel:

```env
APP_ARIMA_WEBSOCKET_URL=wss://localhost:6969
DB_CONNECTION=sqlite
```

---

## 🤝 Kontribusi

Pull request dan feedback sangat diapresiasi! Pastikan menguji fitur sebelum membuat PR.

---

## 📝 Lisensi

Proyek ini dilisensikan di bawah MIT License.
