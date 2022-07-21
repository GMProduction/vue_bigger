<?php

namespace App\Http\Controllers;

use App\Models\keranjang;
use App\Models\Pesanan;
use App\Models\pembayaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PesananController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $pesanan = Pesanan::with('pelanggan')->orderby('created_at', "DESC")->get();
        // dd($pesanan);
        return $pesanan;
    }

    public function showPembayaran()
    {
        $pesan = Pesanan::with(['pelanggan', 'pembayarans' => function ($query) {
            $query->selectRaw('*');
        }])->where('status_bayar', 'belum')->get();

        // $pesanan = Pesanan::with(['pelanggan','pembayarans'])->select('*')->
        // // leftJoin('pembayarans', 'pembayarans.id_pesanan', '=', 'pesanans.id')->
        // where("status_bayar", "belum")
        // ->orderby('created_at', "DESC")->get();

        // dd($pesan);

        return $pesan;
    }

    public function showPesananID($id)
    {
        try {
            $subTotal = keranjang::with('jenisBarang')->where("id_pesanan", $id)->sum('total');
            $dataKeranjang = keranjang::with('jenisBarang')->where("id_pesanan", $id)->orderby('created_at', "DESC")->get();
            $dataPesanan = Pesanan::with('pelanggan')->find($id);
            return response()->json([
                'msg' => 'Berhasil fetch data ',
                'data' => $dataKeranjang,
                'dataPesanan' => $dataPesanan,
                'subTotal' => $subTotal
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'msg' => 'Gagal menambah data' . $e->getMessage()
            ], 500);
        }
    }

    public function showDashboard()
    {
        
        $pesan = Pesanan::with(['pelanggan', 'pembayarans' => function ($query) {
            $query->selectRaw('*');
        }])->where('status_bayar', 'belum')->orwhere("status_proses", "menunggu")->orderBy('deadline', 'ASC')->get();

        // $pesanan = Pesanan::with(['pelanggan','pembayarans'])->select('*')->
        // // leftJoin('pembayarans', 'pembayarans.id_pesanan', '=', 'pesanans.id')->
        // where("status_bayar", "belum")
        // ->orderby('created_at', "DESC")->get();

        // dd($pesan);

        return $pesan;
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

        $ldate = date('Y-m-d');

        try {

            $newItem = new Pesanan([
                'id_pelanggan' => $request->input('id_pelanggan'),
                'diskon' => $request->input('diskon'),
                'sub_total' => $request->input('sub_total'),
                'total_biaya' => $request->input('total_biaya'),
                'status_bayar' =>  'belum',
                'tanggal_pesan' => $ldate,
                'deadline' => $request->input('deadline'),
                'akun' => $request->input('akun'),
                'catatan' => $request->input('catatan'),
            ]);

            $newItem->save();

            $idPesanan = $newItem->id;

            keranjang::where("id_pesanan", null)->where("id_user", auth()->user()->id)->update(['id_pesanan' => $idPesanan]);

            return response()->json([
                'msg' => 'Berhasil menambah data '
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
     * @param  \App\Models\Pesanan  $pesanan
     * @return \Illuminate\Http\Response
     */
    public function show(Pesanan $pesanan)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Pesanan  $pesanan
     * @return \Illuminate\Http\Response
     */
    public function edit(Pesanan $pesanan)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Pesanan  $pesanan
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $newItem = Pesanan::find($id);
            $newItem->update([
                'id_pelanggan' => $request->input('id_pelanggan'),
                'diskon' => $request->input('diskon'),
                'sub_total' => $request->input('sub_total'),
                'total_biaya' => $request->input('total_biaya'),
                'deadline' => $request->input('deadline'),
                'akun' => $request->input('akun'),
                'catatan' => $request->input('catatan'),
            ]);
            $newItem->save();

            // $idPesanan = $newItem->id;

            // keranjang::where("id_pesanan", $id)->where("id_user", auth()->user()->id)->update(['id_pesanan' => $idPesanan]);

            return response()->json([
                'msg' => 'Berhasil merubah data '
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'msg' => 'Gagal menambah data' . $e->getMessage()
            ], 500);
        }
    }

    public function updateStatus(Request $request, $id)
    {
        try {
            DB::transaction(function () use ($id, $request) {
                $newItem = Pesanan::find($id);
                $newItem->update([
                    'status_proses' => $request->input('status_pengerjaan'),
                ]);

                keranjang::where("id_pesanan", $id)->update(['status_pengerjaan' => $request->input('status_pengerjaan')]);

                $newItem->save();
            });
            return response()->json([
                'msg' => 'Berhasil Merubah Satus Pengerjaan '
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'msg' => 'Gagal Merubah Satus Pengerjaan ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Pesanan  $pesanan
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        try {
            DB::transaction(function () use ($id) {

                keranjang::where("id_pesanan", $id)->delete();

                $newItem = Pesanan::find($id);
                $newItem->delete();
            });

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
