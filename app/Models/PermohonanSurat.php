<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PermohonanSurat extends Model
{
    use HasFactory;

    public const STATUS_PERMOHONAN = [
        0 => 'Belum Lengkap',
        1 => 'Sedang Diperiksa',
        2 => 'Menunggu Tandatangan',
        3 => 'Siap Diambil',
        4 => 'Sudah Diambil',
        5 => 'Dibatalkan',
    ];

    /**
     * {@inheritDoc}
     */
    protected $table = 'permohonan_surat';

    /**
     * {@inheritDoc}
     */
    protected $fillable = [
        'id_pemohon',
        'id_surat',
        'isian_form',
        'status',
        'keterangan',
        'no_hp_aktif',
        'syarat',
    ];

    /**
     * {@inheritDoc}
     */
    protected $casts = [
        'isian_form' => 'json',
        'syarat'     => 'json',
    ];

    /**
     * {@inheritDoc}
     */
    protected $with = ['formatSurat', 'penduduk'];

    /**
     * Getter untuk mapping status permohonan.
     *
     * @return string
     */
    public function getStatusPermohonanAttribute()
    {
        return static::STATUS_PERMOHONAN[$this->status];
    }

    /**
     * Setter untuk id surat permohonan.
     *
     * @return void
     */
    public function setIdSuratAttribute(string $slug)
    {
        $this->attributes['id_surat'] = FormatSurat::where('url_surat', $slug)->first()->id;
    }

    /**
     * Scope query untuk pengguna.
     *
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopePengguna($query)
    {
        // return $query->where('id_pemohon', auth('jwt')->user()->penduduk->id);
    }

    public function penduduk()
    {
        return $this->belongsTo(Penduduk::class, 'id_pemohon');
    }

    public function formatSurat()
    {
        return $this->belongsTo(FormatSurat::class, 'id_surat');
    }
}
