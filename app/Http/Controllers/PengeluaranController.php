<?php

namespace App\Http\Controllers;

use App\Models\pengeluaran;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PengeluaranController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return pengeluaran::orderby('created_at', 'DESC')->get();
    }

    public function pencarian(Request $request)
    {
        $subDateThen = substr(Carbon::now(), 0, 10);
        $subDateNow = substr(Carbon::now(), 0, 10);
        $dateBetween = [$subDateThen, $subDateNow];

        if ($request->input('lapTanggal') != null) {
            $subDateThen = $request->input('lapTanggal')[0];

            if (count($request->input('lapTanggal')) == 2) {
                $subDateNow = $request->input('lapTanggal')[1];

                $dateBetween = [$subDateThen, $subDateNow];
            } else {
                $subDateNow = $request->input('lapTanggal')[0];

                $dateBetween = [$subDateThen, $subDateNow];
            }
        }

        $pengeluaran = pengeluaran::whereBetween(
            'tanggal',
            $dateBetween
        )->get();

        $total = pengeluaran::whereBetween('tanggal', $dateBetween)->sum(
            'nominal'
        );

        return response()->json(
            [
                'msg' => 'Berhasil fetch data',
                'pengeluaran' => $pengeluaran,
                'total' => $total,
            ],
            200
        );
    }

    public function pencarianLaporan(Request $request)
    {
        $subDateThen = substr(Carbon::now(), 0, 10);
        $subDateNow = substr(Carbon::now(), 0, 10);
        $dateBetween = [$subDateThen, $subDateNow];

        if ($request->input('lapTanggal') != null) {
            $subDateThen = $request->input('lapTanggal')[0];

            if (count($request->input('lapTanggal')) == 2) {
                $subDateNow = $request->input('lapTanggal')[1];

                $dateBetween = [$subDateThen, $subDateNow];
            } else {
                $subDateNow = $request->input('lapTanggal')[0];

                $dateBetween = [$subDateThen, $subDateNow];
            }
        }

        if ($request->input('akun') == '') {
            $pengeluaran = pengeluaran::whereBetween(
                'tanggal',
                $dateBetween
            )->get();

            $total = pengeluaran::whereBetween('tanggal', $dateBetween)->sum(
                'nominal'
            );
        } else {
            $pengeluaran = pengeluaran::whereBetween('tanggal', $dateBetween)
                ->where('akun', 'LIKE', '%' . $request->input('akun') . '%')
                ->get();

            $total = pengeluaran::whereBetween('tanggal', $dateBetween)
                ->where('akun', 'LIKE', '%' . $request->input('akun') . '%')
                ->sum('nominal');
        }

        return response()->json(
            [
                'msg' => 'Berhasil fetch data',
                'pengeluaran' => $pengeluaran,
                'total' => $total,
            ],
            200
        );
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
            if (!$request->input('isFile')) {
                $generated_new_name = null;
            } else {
                $upload_path = public_path('img/pengeluaran');
                $generated_new_name =
                    time() . '.' . $request->file->getClientOriginalExtension();
                $request->file->move($upload_path, $generated_new_name);
            }

            if (!$request->input('isFile')) {
                $newItem = new pengeluaran([
                    'tanggal' => $request->input('tanggal'),
                    'nominal' => $request->input('nominal'),
                    'keterangan' => $request->input('keterangan'),
                    'akun' => $request->input('akun'),
                ]);
            } else {
                $newItem = new pengeluaran([
                    'tanggal' => $request->input('tanggal'),
                    'nominal' => $request->input('nominal'),
                    'keterangan' => $request->input('keterangan'),
                    'akun' => $request->input('akun'),
                    'urlGambar' => $generated_new_name,
                ]);
            }

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
                    'msg' => 'Gagal menambah data' . $e->getMessage(),
                ],
                500
            );
        }
    }

    public function pengeluaranDashboard()
    {
        $dateS = Carbon::now()
            ->startOfMonth()
            ->subMonth(1);
        $dateE = Carbon::now()
            ->startOfMonth()
            ->addMonth(1);

        $pemasukan = pengeluaran::select(
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
     * Display the specified resource.
     *
     * @param  \App\Models\pengeluaran  $pengeluaran
     * @return \Illuminate\Http\Response
     */
    public function show(pengeluaran $pengeluaran)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\pengeluaran  $pengeluaran
     * @return \Illuminate\Http\Response
     */
    public function edit(pengeluaran $pengeluaran)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\pengeluaran  $pengeluaran
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            if (!$request->input('isFile')) {
                $generated_new_name = null;
                $pesan = 'haha';
            } else {
                $upload_path = public_path('img/pengeluaran');
                $generated_new_name =
                    time() . '.' . $request->file->getClientOriginalExtension();
                $request->file->move($upload_path, $generated_new_name);

                $pesan = 'hihi';
            }

            $newItem = pengeluaran::find($id);
            if (!$request->input('isFile')) {
                $newItem->update([
                    'tanggal' => $request->input('tanggal'),
                    'nominal' => $request->input('nominal'),
                    'keterangan' => $request->input('keterangan'),
                    'akun' => $request->input('akun'),
                ]);
            } else {
                $newItem->update([
                    'tanggal' => $request->input('tanggal'),
                    'nominal' => $request->input('nominal'),
                    'keterangan' => $request->input('keterangan'),
                    'akun' => $request->input('akun'),
                    'urlGambar' => $generated_new_name,
                ]);
            }

            $newItem->save();
            return response()->json(
                [
                    'msg' =>
                        'Berhasil merubah data ' .
                        $pesan .
                        $request->input('urlGambar'),
                ],
                200
            );
        } catch (\Exception $e) {
            return response()->json(
                [
                    'msg' => 'Gagal merubah data ' . $e->getMessage(),
                ],
                500
            );
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\pengeluaran  $pengeluaran
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $newItem = pengeluaran::find($id);
            $newItem->delete();

            return response()->json(
                [
                    'msg' => 'Berhasil menghapus data',
                ],
                200
            );
        } catch (\Exception $e) {
            return response()->json(
                [
                    'msg' => 'Gagal menghapus data ' . $e->getMessage(),
                ],
                500
            );
        }
    }
}
