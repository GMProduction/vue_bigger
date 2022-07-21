<?php

namespace App\Http\Controllers;

use App\Models\keranjang;
use Illuminate\Http\Request;

class KeranjangController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        return keranjang::with('jenisBarang')->orderby('created_at', "DESC")->get();
    }



    public function showNewKeranjang()
    {
        try {
            $subTotal = keranjang::with('jenisBarang')->where("id_pesanan", null)->where("id_user", auth()->user()->id)->sum('total');

            $dataKeranjang = keranjang::with('jenisBarang')->where("id_pesanan", null)->where("id_user", auth()->user()->id)->orderby('created_at', "DESC")->get();

            return response()->json([
                'msg' => 'Berhasil fetch data ',
                'data' => $dataKeranjang,
                'subTotal' => $subTotal
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'msg' => 'Gagal menambah data' . $e->getMessage()
            ], 500);
        }
    }


    public function showKeranjangbyID($id)
    {
        try {
            $subTotal = keranjang::with('jenisBarang')->where("id_pesanan", $id)->sum('total');

            $dataKeranjang = keranjang::with('jenisBarang')->where("id_pesanan", $id)->orderby('created_at', "DESC")->get();

            return response()->json([
                'msg' => 'Berhasil fetch data ',
                'data' => $dataKeranjang,
                'subTotal' => $subTotal
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'msg' => 'Gagal menambah data' . $e->getMessage()
            ], 500);
        }
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


        try {
            $user = auth()->user();

            $newItem = new keranjang([
                'id_jenisBarang' => $request->input('kJenisBarang'),
                'id_user' => $user->id,
                'ukuran' => $request->input('kUkuran'),
                'qty' => $request->input('kQty'),
                'biayaTambahan' => $request->input('kBiayaTambahan'),
                'status_pengerjaan' => 'menunggu',
                'total' => $request->input('kTotal'),
                'catatan' => $request->input('kCatatan'),
            ]);
            $newItem->save();
            return response()->json([
                'msg' => 'Berhasil menambah data ' . $user->id
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'msg' => 'Gagal menambah data' . $e->getMessage()
            ], 500);
        }
    }

    public function storeInEdit(Request $request, $id)
    {


        try {
            $user = auth()->user();

            $newItem = new keranjang([
                'id_jenisBarang' => $request->input('kJenisBarang'),
                'id_user' => $user->id,
                'id_pesanan' => $id,
                'ukuran' => $request->input('kUkuran'),
                'qty' => $request->input('kQty'),
                'biayaTambahan' => $request->input('kBiayaTambahan'),
                'status_pengerjaan' => 'menunggu',
                'total' => $request->input('kTotal'),
                'catatan' => $request->input('kCatatan'),
            ]);
            $newItem->save();
            return response()->json([
                'msg' => 'Berhasil menambah data ' . $user->id
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'msg' => 'Gagal menambah data' . $e->getMessage()
            ], 500);
        }
    }
    /**
     * Display the specified resource.
     *
     * @param  \App\Models\keranjang  $keranjang
     * @return \Illuminate\Http\Response
     */
    public function show(keranjang $keranjang)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\keranjang  $keranjang
     * @return \Illuminate\Http\Response
     */
    public function edit(keranjang $keranjang)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\keranjang  $keranjang
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $user = auth()->user();

            $newItem = keranjang::find($id);
            $newItem->update([
                'id_jenisBarang' => $request->input('kJenisBarang'),
                'id_user' => $user->id,
                'ukuran' => $request->input('kUkuran'),
                'qty' => $request->input('kQty'),
                'biayaTambahan' => $request->input('kBiayaTambahan'),
                'status_pengerjaan' => 'menunggu',
                'total' => $request->input('kTotal'),
                'catatan' => $request->input('kCatatan'),
            ]);
            $newItem->save();
            return response()->json([
                'msg' => 'Berhasil menambah data ' . $user->id
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'msg' => 'Gagal menambah data ' . $id . ' ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateStatus(Request $request, $id)
    {
        try {

            $newItem = keranjang::find($id);
            $newItem->update([
                'status_pengerjaan' => $request->input('status_pengerjaan'),
            ]);
            $newItem->save();
            return response()->json([
                'msg' => 'Berhasil Merubah Satus Pengerjaan ' 
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'msg' => 'Gagal Merubah Satus Pengerjaan '.$e->getMessage() 
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\keranjang  $keranjang
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $newItem = keranjang::find($id);
            $newItem->delete();
            return response()->json([
                'msg' => 'Berhasil menghapus data'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'msg' => 'Gagal menghapus data ' . $e->getMessage()
            ], 500);
        }
    }
}
