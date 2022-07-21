<?php

namespace App\Http\Controllers;

use App\Models\keranjang;
use App\Models\Pelanggan;
use Illuminate\Http\Request;

class PelangganController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Pelanggan::orderby('created_at', "DESC")->get();
    }


       /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function Pencarian()
    {
        return Pelanggan::orderby('id', "ASC")->get();
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try{
            $newItem = new Pelanggan([
                'nama_pelanggan' => $request->input('nama_pelanggan'),
                'no_telp' => $request->input('no_telp'),
                'alamat' => $request->input('alamat'),
            ]);
            $newItem->save();
            return response()->json([
                'msg' => 'Berhasil menambah data'
            ], 200);
        }catch (\Exception $e){
            return response()->json([
                'msg' => 'Gagal menambah data '.$e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Pelanggan  $pelanggan
     * @return \Illuminate\Http\Response
     */
    public function show(Pelanggan $pelanggan)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Pelanggan  $pelanggan
     * @return \Illuminate\Http\Response
     */
    public function edit(Pelanggan $pelanggan)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Pelanggan  $pelanggan
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try{
            $newItem = Pelanggan::find($id);
            $newItem->update([
                'nama_pelanggan' => $request->input('nama_pelanggan'),
                'no_telp' => $request->input('no_telp'),
                'alamat' => $request->input('alamat'),
            ]);
            $newItem->save();
            return response()->json([
                'msg' => 'Berhasil merubah data'
            ], 200);
        }catch (\Exception $e){
            return response()->json([
                'msg' => 'Gagal merubah data '.$e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Pelanggan  $pelanggan
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try{
          
            
            $newItem = Pelanggan::find($id);
            $newItem->delete();
            return response()->json([
                'msg' => 'Berhasil menghapus data'
            ], 200);
        }catch (\Exception $e){
            return response()->json([
                'msg' => 'Gagal menghapus data '.$e->getMessage()
            ], 500);
        }
    }
}
