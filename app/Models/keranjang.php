<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class keranjang extends Model
{
    use HasFactory;
    protected $fillable = ['id_pesanan', 'id_jenisBarang','id_user', 'ukuran', 'qty', 'biayaTambahan','total', 'catatan', 'status_pengerjaan'];
    
    public function jenisBarang()
    {
        return $this->belongsTo(jenisBarang::class, 'id_jenisBarang', 'id');
    }

    public function pesanan()
    {
        return $this->belongsTo(pesanan::class, 'id_pesanan', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id');
    }
}
