<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produk;

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
            return redirect()->route('produk')->with('error', 'Produk tidak ditemukan');
        }
        $produk->delete();
        return redirect()->route('produk')->with('success', 'Produk berhasil dihapus');
    }
    
    public function update(Request $produk, $id)
    {
        $produk = Produk::find($id);
        $produk->update([
            'nama_produk' => $produk['nama_produk'],
            'deskripsi' => $produk['deskripsi'],
            'harga' => $produk['harga'],
            'stok' => $produk['stok'],
            'satuan' => $produk['satuan'],
        ]);
        return redirect()->route('produk')->with('success', 'Produk berhasil diupdate');
    }
}
