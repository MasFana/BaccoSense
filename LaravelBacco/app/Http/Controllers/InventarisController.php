<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use App\Models\Produk;
use App\Models\Inventaris;

class InventarisController extends Controller
{
    // public function index()
    // {
    //     return view('admin.inventaris.index', ['produks' => Produk::latest()->paginate(15)]);
    // }

    public function index()
    {
        $inventaris = Inventaris::with("produk")
            ->orderBy('is_rusak', 'asc')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.inventaris.index', compact('inventaris'));
    }

    public function edit($id)
    {
        $inventaris = Inventaris::findOrFail($id);
        $produks = Produk::orderBy('nama')->get();
        return view('admin.inventaris.edit', compact('inventaris', 'produks'));
    }
    
    
    public function arima()
    {
        try {
            // Fetch forecast data from your Python service
            $response = Http::timeout(30)->get('http://localhost:6942/forecast');

            if ($response->successful()) {
                $forecastData = $response->json();

                // Add current date to the response
                $forecastData['date'] = now()->toDateTimeString();

                return view('admin.inventaris.arima', [
                    'forecastData' => $forecastData
                ]);
            } else {
                Log::error('Forecast service returned error', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);

                return back()->with('error', 'Failed to fetch forecast data. Status: ' . $response->status());
            }
        } catch (\Exception $e) {
            Log::error('Failed to fetch forecast data', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->with('error', 'Failed to connect to forecast service. Please try again later.');
        }
    }
}
