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
    
    public function update(Request $request, $id)
    {
        $request->validate([
            'produk_id' => 'required|exists:produks,id',
            'jumlah' => [
            'required',
            'integer',
            'min:0',
            function($attribute, $value, $fail) use ($request) {
                $produk = Produk::find($request->produk_id);
                if ($produk && $value > $produk->stok) {
                $fail('Jumlah Melebihi stok produk ('.$produk->stok.').');
                }
            }
            ],
            'is_rusak' => 'required|boolean',
        ]);
        // Kurangi stok produk
        $produk = Produk::findOrFail($request->produk_id);
        $inventaris = Inventaris::findOrFail($id);
        
        // Check rusak status
        if ($request->is_rusak) {
            $inventaris->is_rusak = true;
        } else if (!$inventaris->is_rusak) {
            // Hitung selisih stok
            $selisih = $request->jumlah - $inventaris->jumlah;
            // Jika jumlah baru lebih besar, kurangi stok
            // Jika jumlah baru lebih kecil, tambah stok
            $produk->stok -= $selisih;
        }
        
        if ($request->jumlah == 0) {
            $inventaris->delete();
        }else{
            $inventaris->update($request->all());
        }
        
        $produk->save();

        return redirect()->route('inventaris')->with('success', 'Inventaris updated successfully.');
    }
    public function destroy($id)
    {
        $inventaris = Inventaris::findOrFail($id);
        $produk = Produk::findOrFail($inventaris->produk_id);
        $produk->stok += $inventaris->jumlah;
        $produk->save();
        // Delete the inventaris record
        $inventaris->delete();

        return redirect()->route('inventaris')->with('success', 'Inventaris deleted successfully.');
    }
    
    public function tambah()
    {

        $produks = Produk::orderBy('nama')->get();
        return view('admin.inventaris.tambah', compact('produks'));
    }
    public function store(Request $request)
    {

        $request->validate([
            'produk_id' => 'required|exists:produks,id',
            'jumlah' => [
                'required',
                'integer',
                'min:0',
                function ($attribute, $value, $fail) use ($request) {
                    $produk = Produk::find($request->produk_id);
                    if ($produk && $value > $produk->stok) {
                        $fail('Jumlah Melebihi stok produk (' . $produk->stok . ').');
                    }
                }
            ],
            'is_rusak' => 'required|boolean',
        ]);
        // Kurangi stok produk
        $produk = Produk::findOrFail($request->produk_id);
        $produk->stok -= $request->jumlah;
        $produk->save();

        Inventaris::create($request->all());

        return redirect()->route('inventaris')->with('success', 'Inventaris created successfully.');
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
