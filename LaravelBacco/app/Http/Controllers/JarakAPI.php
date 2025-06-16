<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\State_Iot;

class JarakAPI extends Controller
{
    
    public function getJarakKosong()
    {
        $stateIOT = State_Iot::get()->first();
        return response()->json([
            'jarak_kosong' => $stateIOT->state_kosong_penyimpanan,
        ]);
    }
    public function updateJarakKosong(Request $request)
    {
        $stateIOT = State_Iot::get()->first();
        $stateIOT->state_kosong_penyimpanan = $request->input('jarak_kosong');
        $stateIOT->save();

        return response()->json([
            'message' => 'berhasil',
            'jarak_kosong' => $stateIOT->state_kosong_penyimpanan,
        ]);
    }
    
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
