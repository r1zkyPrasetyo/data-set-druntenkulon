<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class twebKeluarga extends Model
{
    use HasFactory;
    protected $table = 'tweb_keluarga';

    /**
     * The timestamps for the model.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The guarded with the model.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Define a one-to-one relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\hasOne
     */
    public function kepalaKeluarga()
    {
        return $this->hasOne(Penduduk::class, 'id', 'nik_kepala');
    }

    /**
     * Define a one-to-many relationship.
     *
     * @return HasMany
     */
    public function anggota()
    {
        return $this->hasMany(Penduduk::class, 'id_kk');
    }

    /**
     * Scope query untuk status keluarga
     *
     * @return Builder
     */
    public function scopeStatus()
    {
        return static::whereHas('kepalaKeluarga', static function ($query) {
            $query->status()->where('kk_level', '1');
        });
    }
}
