<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produk;
use App\Models\Inventaris;

class ProdukController extends Controller
{
    public function index()
    {
        return view('admin.produk.index', [
            'produks' => Produk::latest()->paginate(15),
        ]);
    }

    public function tambah()
    {
        return view('admin.produk.tambah');
    }

    public function edit($id)
    {
        $produk = Produk::find($id);
        return view('admin.produk.edit', compact('produk'));
    }

    public function store(Request $produk)
    {
        $produk->validate([
            'nama_produk' => 'required|string|max:255',
            'deskripsi' => 'required|string|max:1000',
            'harga' => 'required|numeric|min:0',
            'stok' => 'required|integer|min:0',
            'satuan' => 'string|max:50',
        ]);

        Produk::create([
            'nama_produk' => $produk['nama_produk'],
            'deskripsi' => $produk['deskripsi'],
            'harga' => $produk['harga'],
            'stok' => $produk['stok'],
            'satuan' => $produk['satuan'] ?? '100 gram',
        ]);
        return redirect()->route('produk')->with('success', 'Produk berhasil ditambahkan');
    }

    public function destroy($id)
    {
        $produk = Produk::find($id);
        if (!$produk) {
            return redirect()->back()->with('error', 'Produk tidak ditemukan');
        }
        // Check if the product has related records in other tables
        Inventaris::where('produk_id', $id)->delete();
        $produk->delete();
        return redirect()->back()->with('success', 'Produk berhasil dihapus');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_produk' => 'required|string|max:255',
            'deskripsi' => 'required|string|max:1000',
            'harga' => 'required|numeric|min:0',
            'stok' => 'required|integer|min:0',
            'satuan' => 'string|max:50',
        ]);

        $produk = Produk::find($id);
        $produk->update([
            'nama_produk' => $request['nama_produk'],
            'deskripsi' => $request['deskripsi'],
            'harga' => $request['harga'],
            'stok' => $request['stok'],
            'satuan' => $request['satuan'],
        ]);
        return redirect()->route('produk')->with('success', 'Produk berhasil diupdate');
    }
}

