import sqlite3
import random
import pandas as pd
from datetime import datetime
import pmdarima as pm
import json
import os
import warnings

# Suppress warnings for a cleaner output
warnings.filterwarnings("ignore", category=FutureWarning)
warnings.filterwarnings("ignore", category=RuntimeWarning, message="divide by zero encountered in reciprocal")

# Path to the cache file
CACHE_FILE_PATH = 'forecast_cache.json'

def load_cache():
    """Load cached forecast data if available and valid"""
    if os.path.exists(CACHE_FILE_PATH):
        with open(CACHE_FILE_PATH, 'r') as cache_file:
            cached_data = json.load(cache_file)
            
            # Check if the cache is from today
            cache_date = cached_data.get('date', None)
            today_date = datetime.today().strftime('%Y-%m-%d')
            
            if cache_date == today_date:
                return cached_data  # Cache is valid
    return None  # No valid cache or cache doesn't exist

def save_cache(data):
    """Save forecast data to cache"""
    with open(CACHE_FILE_PATH, 'w') as cache_file:
        # Add the current date to the cache to validate it later
        data['date'] = datetime.today().strftime('%Y-%m-%d')
        json.dump(data, cache_file, indent=4)

def generate_forecasts():
    """Generate forecasts and return the results"""
    # Connect to your SQLite DB
    conn = sqlite3.connect('../LaravelBacco/database/database.sqlite')
    cursor = conn.cursor()

    # Fetch product names
    cursor.execute("SELECT DISTINCT nama_produk FROM produks")
    products = [row[0] for row in cursor.fetchall()]
    conn.close()

    # Generate 12 months of data (dates on 1st of each month)
    today = datetime.today().replace(day=1)
    dates = pd.date_range(end=today, periods=12, freq='MS')

    # Build DataFrame with random sales for each product
    all_data = []

    for produk in products:
        sales = [random.randint(100, 1000) for _ in range(12)]
        df_produk = pd.DataFrame({
            'tanggal': dates,
            'penjualan': sales,
            'nama_produk': produk
        })
        all_data.append(df_produk)

    # Combine all into a single DataFrame
    df = pd.concat(all_data).reset_index(drop=True)

    # Forecast 3 months ahead for each product
    forecasts_per_produk = {}
    produk_naik = []
    produk_turun = []

    for produk in df['nama_produk'].unique():
        df_produk = df[df['nama_produk'] == produk].copy()
        df_produk.set_index('tanggal', inplace=True)
        df_produk = df_produk.sort_index()

        # Train ARIMA model
        model = pm.auto_arima(df_produk['penjualan'],
                              start_p=1, start_q=1,
                              test='adf',
                              max_p=3, max_q=3,
                              m=12,
                              d=None, seasonal=True,
                              start_P=0, D=0,
                              error_action='ignore', suppress_warnings=True, stepwise=True)

        # Forecast 3 months
        forecast = model.predict(n_periods=3)
        forecast_dates = pd.date_range(start=df_produk.index[-1] + pd.DateOffset(months=1), periods=3, freq='MS')
        forecast = [round(val) for val in forecast]

        forecasts_per_produk[produk] = pd.Series(forecast, index=forecast_dates)

        # Analyze trend: whether it will increase or decrease
        if forecast[-1] > df_produk['penjualan'].iloc[-1]:
            produk_naik.append(produk)
        else:
            produk_turun.append(produk)

    # Prepare the result to return in JSON format
    result = {
        "forecasts": {produk: forecast_series.tolist() for produk, forecast_series in forecasts_per_produk.items()},
        "produk_naik": produk_naik,
        "produk_turun": produk_turun
    }

    return result

# Check if cache exists and is valid
cached_data = load_cache()

if cached_data:
    # Use cached data if available
    result = cached_data
else:
    # No valid cache, generate forecasts
    result = generate_forecasts()
    # Save the generated forecasts to cache
    save_cache(result)

# Output the result as JSON
print(json.dumps(result, indent=4))
