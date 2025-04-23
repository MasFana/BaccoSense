<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produk;

class InventarisController extends Controller
{
    public function index()
    {
        return view('admin.inventaris.index', ['produks'=>Produk::latest()->paginate(15)]);
    }

    public function arima(){
        return view('admin.inventaris.arima', [
            'produks' => Produk::latest()->paginate(15),
        ]);
    }
    
}
