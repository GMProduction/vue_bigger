<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class jenisBarang extends Model
{
    protected $fillable = ['nama', 'satuan', 'harga'];

    public function keranjangs()
    {
        return $this->hasMany(keranjangs::class);
    }
}
