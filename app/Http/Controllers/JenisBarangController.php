<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\jenisBarang;

class JenisBarangController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return JenisBarang::orderby('created_at', "DESC")->get();
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function Pencarian()
    {
        return jenisBarang::orderby('id', "ASC")->get();
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
            $newItem = new jenisBarang([
                'nama' => $request->input('nama'),
                'satuan' => $request->input('satuan'),
                'harga' => $request->input('harga'),
            ]);
            $newItem->save();
            return response()->json([
                'msg' => 'Berhasil menambah data'
            ], 200);
        }catch (\Exception $e){
            return response()->json([
                'msg' => 'Gagal menambah data'.$e->getMessage()
            ], 500);
        }
       
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
    
    
        try{
            $newItem = jenisBarang::find($id);
            $newItem->update([
                'nama' => $request->input('nama'),
                'satuan' => $request->input('satuan'),
                'harga' => $request->input('harga'),
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try{
            $newItem = jenisBarang::find($id);
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
