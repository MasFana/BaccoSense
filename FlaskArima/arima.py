import sqlite3
import random
import pandas as pd
from datetime import datetime
import pmdarima as pm
import json
import os
import warnings
import flask
import numpy as np
from sklearn.metrics import mean_squared_error

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
    """Generate forecasts and return results with RMSE percentage"""
    # Connect to SQLite DB
    conn = sqlite3.connect('../LaravelBacco/database/database.sqlite')
    cursor = conn.cursor()

    # Fetch product names
    cursor.execute("SELECT DISTINCT nama_produk FROM produks")
    products = [row[0] for row in cursor.fetchall()]
    conn.close()
    \
    # Assuming you have a products list defined
    # products = ['Product A', 'Product B', 'Product C']  # Add your products here

    today = datetime.today().replace(day=1)
    dates = pd.date_range(end=today, periods=36, freq='MS')  # 3 years of data

    all_data = []

    for produk in products:
        # Base parameters optimized for ARIMA
        base_sales = random.randint(400, 800)
        trend_slope = random.uniform(-0.01, 0.01)  # Gentle linear trend
        noise_std = random.uniform(0.03, 0.08)  # Lower noise for better predictability
        
        # Seasonal parameters
        seasonal_strength = random.uniform(0.6, 1.0)
        seasonal_phase = random.uniform(0, 2 * np.pi)  # Random phase shift
        
        # Generate base trend component
        trend = np.linspace(0, trend_slope * 36, 36)
        
        # Generate seasonal component for all 36 months
        seasonal = np.array([
            seasonal_strength * np.sin(2 * np.pi * i / 12 + seasonal_phase) 
            for i in range(36)
        ])
        
        # Generate ARIMA-friendly noise with AR(1) structure
        ar_coef = random.uniform(0.2, 0.6)  # AR coefficient
        ma_coef = random.uniform(-0.3, 0.3)  # MA coefficient
        
        # White noise
        white_noise = np.random.normal(0, noise_std, 37)  # Extra for MA component
        
        # Generate ARMA noise
        arma_noise = np.zeros(36)
        arma_noise[0] = white_noise[0]
        
        for i in range(1, 36):
            # AR(1) + MA(1) structure
            arma_noise[i] = (ar_coef * arma_noise[i-1] + 
                            white_noise[i] + 
                            ma_coef * white_noise[i-1])
        
        # Combine all components
        # Using multiplicative seasonal model: base * (1 + trend) * (1 + seasonal) * (1 + noise)
        sales = base_sales * (1 + trend) * (1 + 0.2 * seasonal) * (1 + arma_noise)
        
        # Add some non-linearity occasionally
        if random.random() < 0.3:  # 30% chance of slight non-linearity
            quadratic_term = random.uniform(-0.0001, 0.0001)
            time_squared = np.arange(36) ** 2
            sales *= (1 + quadratic_term * time_squared)
        
        # Apply realistic constraints and ensure integer values
        sales = np.round(np.clip(sales, 100, 1500)).astype(int)
        
        # Optional: Add occasional outliers (ARIMA can handle some)
        if random.random() < 0.2:  # 20% chance of having outliers
            outlier_indices = random.sample(range(5, 31), random.randint(1, 3))
            for idx in outlier_indices:
                outlier_multiplier = random.choice([0.7, 0.8, 1.2, 1.3])
                sales[idx] = int(sales[idx] * outlier_multiplier)
        
        # Create DataFrame for this product
        df_produk = pd.DataFrame({
            'tanggal': dates,
            'penjualan': sales,
            'nama_produk': produk
        })
        all_data.append(df_produk)

    df = pd.concat(all_data).reset_index(drop=True)

    # Forecasting
    forecasts_per_produk = {}
    rmse_per_produk = {}
    rmse_percentage = {}
    produk_naik = []
    produk_turun = []
    mean_sales = df.groupby('nama_produk')['penjualan'].mean()

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
                             error_action='ignore', 
                             suppress_warnings=True, 
                             stepwise=True)

        # Calculate RMSE
        in_sample_pred = model.predict_in_sample()
        rmse = np.sqrt(mean_squared_error(df_produk['penjualan'], in_sample_pred))
        rmse_per_produk[produk] = round(rmse, 2)
        rmse_percentage[produk] = round((rmse / mean_sales[produk]) * 100, 1)

        # Forecast 3 months
        forecast = model.predict(n_periods=3)
        forecast = np.round(np.maximum(0, forecast)).astype(int)  # Ensure non-negative

        forecast_dates = pd.date_range(start=df_produk.index[-1] + pd.DateOffset(months=1), 
                                     periods=3, freq='MS')
        forecasts_per_produk[produk] = pd.Series([round(val) for val in forecast], 
                                               index=forecast_dates)

        # Trend analysis
        if forecast[-1] > df_produk['penjualan'].iloc[-1]:
            produk_naik.append(produk)
        else:
            produk_turun.append(produk)

    # Prepare results
    result = {
        "metadata": {
            "generated_at": datetime.now().strftime('%Y-%m-%d %H:%M:%S'),
            "forecast_horizon": 3,
            "model": "ARIMA"
        },
        "original_data": json.loads(df.to_json(orient='records', date_format='iso')),
        "forecasts": {
            produk: {
                "values": forecast_series.tolist(),
                "dates": forecast_series.index.strftime('%Y-%m-%d').tolist()
            } for produk, forecast_series in forecasts_per_produk.items()
        },
        "metrics": {
            "rmse": rmse_per_produk,
            "rmse_percentage": rmse_percentage,
            "mean_sales": mean_sales.to_dict(),
            "interpretation": {
                produk: "Baik" if perc < 20 else 
                       "Normal" if perc < 30 else 
                       "Kurang Baik"
                for produk, perc in rmse_percentage.items()
            }
        },
        "trend_analysis": {
            "increasing": produk_naik,
            "decreasing": produk_turun
        }
    }

    return result

# def generate_forecasts():
    """Generate forecasts and return the results"""
    # Connect to  SQLite DB
    conn = sqlite3.connect('../LaravelBacco/database/database.sqlite')
    # conn = sqlite3.connect(database='../../../database/database.sqlite')
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

    # for produk in products:
    #     sales = [random.randint(100, 1000) for _ in range(12)]
    #     df_produk = pd.DataFrame({
    #         'tanggal': dates,
    #         'penjualan': sales,
    #         'nama_produk': produk
    #     })
    #     all_data.append(df_produk)
    
    for produk in products:
        # Base sales with product-specific variations
        base_sales = random.randint(300, 700)
        
        # Create seasonal pattern (e.g., higher sales in certain months)
        seasonal_pattern = np.array([1.0, 1.1, 1.3, 1.2, 1.0, 0.9, 
                                0.8, 0.9, 1.0, 1.2, 1.3, 1.1])
        
        # Add some randomness and trend
        trend = np.linspace(0, random.uniform(-0.2, 0.2), 12)
        noise = np.random.normal(0, 0.1, 12)
        
        # Combine components
        sales = base_sales * seasonal_pattern * (1 + trend + noise)
        sales = np.round(sales).astype(int)
        
        # Ensure no negative values
        sales = np.clip(sales, 100, 1000)
        
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
    rmse_per_produk = {}
    produk_naik = []
    produk_turun = []

    mean_sales = df.groupby('nama_produk')['penjualan'].mean()
    
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

        # Predict in-sample to calculate RMSE
        in_sample_pred = model.predict_in_sample()
        rmse = np.sqrt(mean_squared_error(df_produk['penjualan'], in_sample_pred))
        rmse_per_produk[produk] = round(rmse, 2)
        
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

    print(df.to_dict(orient='records'))
    # Prepare the result to return in JSON format
    result = {    
        "original_data": json.loads( df.to_json(orient='records')),
        "forecasts": {produk: forecast_series.tolist() for produk, forecast_series in forecasts_per_produk.items()},
        "rmse": rmse_per_produk,
        "produk_naik": produk_naik,
        "produk_turun": produk_turun
    }

    return result

# # Check if cache exists and is valid
# cached_data = load_cache()

# if cached_data:
#     # Use cached data if available
#     result = cached_data
# else:
#     # No valid cache, generate forecasts
#     result = generate_forecasts()
#     # Save the generated forecasts to cache
#     save_cache(result)

# # Output the result as JSON
# print(json.dumps(result, indent=4))


# Flask app to serve the forecast data
app = flask.Flask(__name__)

@app.route('/forecast', methods=['GET'])
def forecast():
    """Endpoint to get the forecast data"""
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

    # Return the result as JSON response
    return flask.jsonify(result)

if __name__ == "__main__":
    app.run(host="localhost", port=6942, debug=True)

