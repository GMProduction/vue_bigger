<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pelanggan extends Model
{
    protected $fillable = ['nama_pelanggan', 'no_telp', 'alamat'];

    public function pesanans()
    {
        return $this->hasMany(Pesanan::class);
    }

    
}
