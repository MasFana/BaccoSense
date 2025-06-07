<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Penjualan;

class PenjualanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $penjualans = Penjualan::with('produk')->latest()->paginate(15);
        // return dd($penjualans);
        return view('admin.penjualan.index', compact('penjualans'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $produk = \App\Models\Produk::all();
        return view('admin.penjualan.tambah', compact('produk'));
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
        $produk = \App\Models\Produk::findOrFail($request->produk_id);
        if ($produk->stok < $request->jumlah) {
            return redirect()->back()->with('error', 'Stok produk tidak cukup');
        }

        Penjualan::create([
            'produk_id' => $request->produk_id,
            'jumlah' => $request->jumlah,
            'harga' => $request->harga,
        ]);

        // Kurangi stok produk
        $produk->stok -= $request->jumlah;
        $produk->save();

        return redirect()->route('penjualan.index')->with('success', 'Penjualan berhasil ditambahkan');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $penjualan = Penjualan::findOrFail($id)->load('produk');
        if ($penjualan->created_at->lt(now()->subHour())) {
            return redirect()->back()->with('error', 'Penjualan yang sudah lebih dari 1 jam tidak dapat diubah');
        }

        return view('admin.penjualan.edit', compact('penjualan'));
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

        $penjualan = Penjualan::findOrFail($id);

        if ($penjualan->created_at->lt(now()->subHour())) {
            return redirect()->back()->with('error', 'Penjualan yang sudah lebih dari 1 jam tidak dapat diubah');
        }

        // Cek apakah stok cukup
        $produk = \App\Models\Produk::findOrFail($request->produk_id);
        if ($produk->stok + $penjualan->jumlah < $request->jumlah) {
            return redirect()->back()->with('error', 'Stok produk tidak cukup');
        }
        // Update stok produk dengan selisih jumlah
        $produk->stok += ($penjualan->jumlah - $request->jumlah);
        $produk->save();

        $penjualan->update([
            'produk_id' => $request->produk_id,
            'jumlah' => $request->jumlah,
            'harga' => $request->harga,
        ]);

        return redirect()->route('penjualan.index')->with('success', 'Penjualan berhasil diperbarui');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $penjualan = Penjualan::findOrFail($id);

        if ($penjualan->created_at->lt(now()->subHour())) {
            return redirect()->back()->with('error', 'Penjualan yang sudah lebih dari 1 jam tidak dapat dihapus');
        }

        $penjualan->delete();

        return redirect()->route('penjualan.index')->with('success', 'Penjualan berhasil dihapus');
    }
}
