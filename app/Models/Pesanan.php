<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pesanan extends Model
{
    use HasFactory;
    protected $fillable = ['id_pelanggan',  'sub_total', 'diskon', 'catatan', 'total_biaya',  'status_bayar', 'status_proses', 'tanggal_pesan', 'deadline', 'akun'];

    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class, 'id_pelanggan', 'id');
    }

    public function keranjangs()
    {
        return $this->hasMany(keranjang::class);
    }
    
    public function pembayarans()
    {
        return $this->hasMany(pembayaran::class, 'id_pesanan', 'id');
    }

   
}
