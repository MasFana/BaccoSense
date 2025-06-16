<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SuhuKelembaban;

class AdminController extends Controller
{
    public function index()
    {
        $history = SuhuKelembaban::latest('created_at')
            ->take(10)->orderBy('created_at', 'desc')
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
            return response()->json([
                'error' => 'Data for this hour already exists.',
            ], 400);
        }
        SuhuKelembaban::create($data);
        return response()->json([
            'message' => 'Data added successfully.',
            'data' => $data,
        ]);
    }
}
