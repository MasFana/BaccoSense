<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produk;
use App\Models\Pembelian;

class PembelianController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pembelians = Pembelian::with('produk')->latest()->paginate(15);

        return view('admin.pembelian.index', compact('pembelians'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $produk = Produk::all();
        return view('admin.pembelian.tambah', compact('produk'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'produk_id' => 'required|exists:produks,id',
            'jumlah' => 'required|integer|min:1',
            'harga' => 'required|numeric|min:0',
        ]);

        // Cek apakah stok cukup
        $produk = Produk::findOrFail($request->produk_id);
        $produk->stok += $request->jumlah;
        
        Pembelian::create([
            'produk_id' => $request->produk_id,
            'jumlah' => $request->jumlah,
            'harga' => $request->harga,
        ]);
        $produk->save();
        

        return redirect()->route('pembelian.index')->with('success', 'Pembelian berhasil ditambahkan');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $pembelian = Pembelian::findOrFail($id);

        if ($pembelian->created_at->lt(now()->subHour())) {
            return redirect()->back()->with('error', 'Pembelian tidak dapat diubah karena sudah melebihi 1 jam');
        }

        $produk = Produk::all();

        return view('admin.pembelian.edit', compact('pembelian', 'produk'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'produk_id' => 'required|exists:produks,id',
            'jumlah' => 'required|integer|min:1',
            'harga' => 'required|numeric|min:0',
        ]);

        $pembelian = Pembelian::findOrFail($id);

        if ($pembelian->created_at->lt(now()->subHour())) {
            return redirect()->back()->with('error', 'Pembelian yang sudah lebih dari 1 jam tidak dapat diubah');
        }

        // Cek apakah stok cukup
        $produk = Produk::findOrFail($request->produk_id);
 
        // Update stok produk dengan selisih jumlah
        $produk->stok += ($pembelian->jumlah - $request->jumlah);
        $produk->save();

        $pembelian->update([
            'produk_id' => $request->produk_id,
            'jumlah' => $request->jumlah,
            'harga' => $request->harga,
        ]);

        return redirect()->route('pembelian.index')->with('success', 'Pembelian berhasil diperbarui');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $pembelian = Pembelian::findOrFail($id);

        if ($pembelian->created_at->lt(now()->subHour())) {
            return redirect()->back()->with('error', 'Pembelian tidak dapat dihapus karena sudah melebihi 1 jam');
        }

        // Kembalikan stok produk
        $produk = Produk::findOrFail($pembelian->produk_id);
        $produk->stok -= $pembelian->jumlah;
        $produk->save();
        $pembelian->delete();

        return redirect()->route('pembelian.index')->with('success', 'Pembelian berhasil dihapus');
    }
}
