<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\JenisBarangController;
use App\Http\Controllers\PelangganController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\KeranjangController;
use App\Http\Controllers\PesananController;
use App\Http\Controllers\PembayaranController;
use App\Http\Controllers\PengeluaranController;

// header('Access-Control-Allow-Origin: *');
// header('Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE');
// header('Access-Control-Allow-Headers: Content-Type, X-Auth-Token, Origin, Authorization');

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
 */

Route::middleware('auth:api')->prefix('v1')->group(function () {
    Route::get('/jenis-barang', [JenisBarangController::class, 'index']);
    Route::prefix("/jenis-barang")->group(
        function () {
            Route::get('/pencarian', [JenisBarangController::class, 'Pencarian']);
            Route::post('/store', [JenisBarangController::class, 'store']);
            Route::put('/update/{id}', [JenisBarangController::class, 'update']);
            Route::delete('/delete/{id}', [JenisBarangController::class, 'destroy']);
        }
    );

    Route::get('/pengeluaran', [PengeluaranController::class, 'index']);
    Route::prefix("/pengeluaran")->group(
        function () {
            Route::post('/store', [PengeluaranController::class, 'store']);
            Route::post('/update/{id}', [PengeluaranController::class, 'update']);
            Route::delete('/delete/{id}', [PengeluaranController::class, 'destroy']);
            Route::get('/pengeluaranDashboard', [PengeluaranController::class, 'pengeluaranDashboard']);
            Route::post('/pencarian', [PengeluaranController::class, 'pencarian']);
            Route::post('/pencarianLaporan', [PengeluaranController::class, 'pencarianLaporan']);
        }
    );

    Route::get('/pelanggan', [PelangganController::class, 'index']);
    Route::prefix("/pelanggan")->group(
        function () {
            Route::get('/pencarian', [PelangganController::class, 'Pencarian']);
            Route::post('/store', [PelangganController::class, 'store']);
            Route::put('/update/{id}', [PelangganController::class, 'update']);
            Route::delete('/delete/{id}', [PelangganController::class, 'destroy']);
        }
    );

    Route::get('/pesanan', [PesananController::class, 'index']);
    Route::prefix("/pesanan")->group(
        function () {
            Route::get('/showPesananID/{id}', [PesananController::class, 'showPesananID']);
            Route::get('/showPembayaran', [PesananController::class, 'showPembayaran']);
            Route::get('/showDashboard', [PesananController::class, 'showDashboard']);
            Route::post('/store', [PesananController::class, 'store']);
            Route::put('/update/{id}', [PesananController::class, 'update']);
            Route::delete('/delete/{id}', [PesananController::class, 'destroy']);
            Route::put('/updateStatus/{id}', [PesananController::class, 'updateStatus']);
            
        }

    );

    Route::get('/pembayaran', [PembayaranController::class, 'index']);
    Route::prefix("/pembayaran")->group(
        function () {
            Route::post('/store', [PembayaranController::class, 'store']);
            Route::post('/storeLunas', [PembayaranController::class, 'storeLunas']);
            Route::put('/update/{id}', [PembayaranController::class, 'update']);
            Route::get('/pemasukanDashboard', [PembayaranController::class, 'pemasukanDashboard']);
            Route::post('/pencarian', [PembayaranController::class, 'pencarian']);
            Route::post('/pencarianLaporan', [PembayaranController::class, 'pencarianLaporan']);
            Route::delete('/delete/{id}/{idPesanan}', [PembayaranController::class, 'destroy']);
        }

    );

    Route::get('/keranjang', [KeranjangController::class, 'index']);
    Route::prefix("/keranjang")->group(
        function () {
            Route::get('/showKeranjangbyID/{id}', [KeranjangController::class, 'showKeranjangbyID']);
            Route::get('/showNewKeranjang', [KeranjangController::class, 'showNewKeranjang']);
            Route::post('/storeInEdit/{id}', [KeranjangController::class, 'storeInEdit']);
            Route::post('/store', [KeranjangController::class, 'store']);
            Route::put('/update/{id}', [KeranjangController::class, 'update']);
            Route::put('/updateStatus/{id}', [KeranjangController::class, 'updateStatus']);
            Route::delete('/delete/{id}', [KeranjangController::class, 'destroy']);
        }

    );
});

Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'

], function ($router) {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::get('/user-profile', [AuthController::class, 'userProfile']);
});
