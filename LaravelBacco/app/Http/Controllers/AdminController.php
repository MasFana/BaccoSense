<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SuhuKelembaban;

class AdminController extends Controller
{
    public function index()
    {
        $history = SuhuKelembaban::orderBy('created_at', 'asc')
            ->take(10)
            ->get();
        return view('admin.dashboard', compact('history'));
    }
    public function add_history(Request $request)
    {
        $data = $request->validate([
            'suhu' => 'required|numeric',
            'kelembaban' => 'required|numeric',
        ]);

        $lastData = SuhuKelembaban::latest()->first();
        if ($lastData && date('Y-m-d H', strtotime($lastData->created_at)) === date('Y-m-d H')) {
            return redirect()->back()->with('error', 'Data for this hour already exists.');
        }
        SuhuKelembaban::create($data);

        return redirect()->back()->with('success', 'Data added successfully.');
    }
}
