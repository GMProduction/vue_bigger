<?php

namespace App\Http\Controllers;

use App\Models\pembayaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Pesanan;
use Carbon\Carbon;

class PembayaranController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $pembayaran = pembayaran::with('pesanan.pelanggan')->get();
        return $pembayaran;
    }

    public function pencarian(Request $request)
    {
        $subDateThen = substr(Carbon::now(), 0, 10) . ' 00:00:00';
        $subDateNow = substr(Carbon::now(), 0, 10) . ' 23:59:59';
        $dateBetween = [$subDateThen, $subDateNow];
        $akun = $request->input('akun');

        if ($request->input('lapTanggal') != null) {
            $subDateThen = $request->input('lapTanggal')[0] . ' 00:00:00';

            if (count($request->input('lapTanggal')) == 2) {
                $subDateNow = $request->input('lapTanggal')[1] . ' 23:59:59';

                $dateBetween = [$subDateThen, $subDateNow];
            } else {
                $subDateNow = $request->input('lapTanggal')[0] . ' 23:59:59';

                $dateBetween = [$subDateThen, $subDateNow];
            }
        }

        $pembayaran = pembayaran::with('pesanan.pelanggan')
            ->whereBetween('created_at', $dateBetween)
            ->get();

         
        // $pembayaran = pembayaran::with("pesanan" ,function($q) use($request){
        //     $q->where('akun','LIKE',$request->input('akun'));
        // })->get();

        $total = pembayaran::whereBetween('created_at', $dateBetween)
            ->sum('nominal');

        return response()->json(
            [
                'msg' => 'Berhasil fetch data',
                'akunnya' => $pembayaran,
                'pembayaran' => $pembayaran,
                'total' => $total,
            ],
            200
        );
    }

    public function pencarianLaporan(Request $request)
    {
        $subDateThen = substr(Carbon::now(), 0, 10) . ' 00:00:00';
        $subDateNow = substr(Carbon::now(), 0, 10) . ' 23:59:59';
        $dateBetween = [$subDateThen, $subDateNow];
        $akun = $request->input('akun');

        if ($request->input('lapTanggal') != null) {
            $subDateThen = $request->input('lapTanggal')[0] . ' 00:00:00';

            if (count($request->input('lapTanggal')) == 2) {
                $subDateNow = $request->input('lapTanggal')[1] . ' 23:59:59';

                $dateBetween = [$subDateThen, $subDateNow];
            } else {
                $subDateNow = $request->input('lapTanggal')[0] . ' 23:59:59';

                $dateBetween = [$subDateThen, $subDateNow];
            }
        }

       if($request->input('akun') == ''){
        $pembayaran = DB::table('pembayarans')
        ->join('pesanans', 'pembayarans.id_pesanan', '=', 'pesanans.id')
        ->join('pelanggans', 'pesanans.id_pelanggan', '=', 'pelanggans.id')
        ->select('pembayarans.*', 'pesanans.*', 'pelanggans.*')
        ->whereBetween('pembayarans.created_at', $dateBetween)
        ->get();
       }else{
        $pembayaran = DB::table('pembayarans')
        ->join('pesanans', 'pembayarans.id_pesanan', '=', 'pesanans.id')
        ->join('pelanggans', 'pesanans.id_pelanggan', '=', 'pelanggans.id')
        ->select('pembayarans.*', 'pesanans.*', 'pelanggans.*')
        ->where('pesanans.akun', 'LIKE','%'.$request->input('akun').'%')
        ->whereBetween('pembayarans.created_at', $dateBetween)
        ->get();
       }

       if($request->input('akun') == ''){
        $total = DB::table('pembayarans')
        ->join('pesanans', 'pembayarans.id_pesanan', '=', 'pesanans.id')
        ->join('pelanggans', 'pesanans.id_pelanggan', '=', 'pelanggans.id')
        ->select('pembayarans.*', 'pesanans.*', 'pelanggans.*')
        ->whereBetween('pembayarans.created_at', $dateBetween)
        ->sum('pembayarans.nominal');
       }else{
        $total = DB::table('pembayarans')
        ->join('pesanans', 'pembayarans.id_pesanan', '=', 'pesanans.id')
        ->join('pelanggans', 'pesanans.id_pelanggan', '=', 'pelanggans.id')
        ->select('pembayarans.*', 'pesanans.*', 'pelanggans.*')
        ->where('pesanans.akun', 'LIKE','%'.$request->input('akun').'%')
        ->whereBetween('pembayarans.created_at', $dateBetween)
        ->sum('pembayarans.nominal');
       }
            

    

        return response()->json(
            [
                'msg' => 'Berhasil fetch data',
                'akunnya' => $pembayaran,
                'pembayaran' => $pembayaran,
                'total' => $total,
            ],
            200
        );
    }
    
    public function pemasukanDashboard()
    {
        $dateS = Carbon::now()
            ->startOfMonth()
            ->subMonth(1);
        $dateE = Carbon::now()
            ->startOfMonth()
            ->addMonth(1);

        $pemasukan = pembayaran::select(
            DB::raw('sum(nominal) as sums'),
            DB::raw("DATE_FORMAT(created_at,'%M %Y') as months")
        )
            ->whereBetween('created_at', [$dateS, $dateE])
            ->groupBy('months')
            ->orderBy('created_at', 'DESC')
            ->get();

        return $pemasukan;
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
            $newItem = new pembayaran([
                'nominal' => $request->input('nominal'),
                'id_pesanan' => $request->input('id_pesanan'),
            ]);
            $newItem->save();
            return response()->json(
                [
                    'msg' => 'Berhasil menambah data',
                ],
                200
            );
        } catch (\Exception $e) {
            return response()->json(
                [
                    'msg' => 'Gagal menambah data ' . $e->getMessage(),
                ],
                500
            );
        }
    }

    public function storeLunas(Request $request)
    {
        try {
            DB::transaction(function () use ($request) {
                $newItem = new pembayaran([
                    'nominal' => $request->input('nominal'),
                    'id_pesanan' => $request->input('id_pesanan'),
                ]);
                $newItem->save();

                $pesanan = Pesanan::find($request->input('id_pesanan'));
                $pesanan->update([
                    'status_bayar' => $request->input('status_bayar'),
                ]);
                $pesanan->save();
            });
            return response()->json(
                [
                    'msg' => 'Berhasil menambah data',
                ],
                200
            );
        } catch (\Exception $e) {
            return response()->json(
                [
                    'msg' => 'Gagal menambah data ' . $e->getMessage(),
                ],
                500
            );
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\pembayaran  $pembayaran
     * @return \Illuminate\Http\Response
     */
    public function show(pembayaran $pembayaran)
    {
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\pembayaran  $pembayaran
     * @return \Illuminate\Http\Response
     */
    public function edit(pembayaran $pembayaran)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\pembayaran  $pembayaran
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, pembayaran $pembayaran)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\pembayaran  $pembayaran
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, $idPesanan)
    {
        try {
            DB::transaction(function () use ($id, $idPesanan) {
                $newItem = pembayaran::find($id);
                $newItem->delete();

                $pesanan = Pesanan::find($idPesanan);
                $pesanan->update([
                    'status_bayar' => 'belum',
                ]);
                $pesanan->save();
            });
            return response()->json(
                [
                    'msg' => 'Berhasil Menghapus data',
                ],
                200
            );
        } catch (\Exception $e) {
            return response()->json(
                [
                    'msg' => 'Gagal Menghapus data ' . $e->getMessage(),
                ],
                500
            );
        }
    }
}
