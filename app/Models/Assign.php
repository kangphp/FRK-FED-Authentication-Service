<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assign extends Model
{
    use HasFactory;

    protected $table = 'assign';

    protected $fillable = [
        'id_pegawai',
        'tipe_asesor',
        'id_tanggal_frk',
        'id_tanggal_fed',
        'program_studi',
        'fakultas',
    ];
}
