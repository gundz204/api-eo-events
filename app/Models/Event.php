<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = [
        'nama',
        'deskripsi',
        'lokasi',
        'jenis',
        'waktu_mulai',
        'waktu_selesai',
        'kuota',
        'mengeluarkan_sertifikat',
        'image',
        'form_pendaftaran',
        'is_active',
        'foto'
    ];
}
