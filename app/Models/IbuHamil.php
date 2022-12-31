<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IbuHamil extends Model
{
    use HasFactory;
    /**
     * {@inheritDoc}
     */
    protected $table = 'ibu_hamil';

    /**
     * Scope query untuk status penduduk
     *
     * @param Builder $query
     * @param mixed   $value
     *
     * @return Builder
     */
    public function scopeStatus($query)
    {
        return $query->where('created_at',Carbon::now());
    }
}
