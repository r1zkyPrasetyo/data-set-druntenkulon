<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dtks extends Model
{
    use HasFactory;

     /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'dtks';

    public function penduduk()
    {
        return $this->belongsTo(Penduduk::class, 'id_pend');
    }
}
