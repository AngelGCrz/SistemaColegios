<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Nivel extends Model
{
    protected $table = 'niveles';

    protected $fillable = [
        'colegio_id', 'nombre', 'orden',
    ];

    public function colegio(): BelongsTo
    {
        return $this->belongsTo(Colegio::class);
    }

    public function grados(): HasMany
    {
        return $this->hasMany(Grado::class)->orderBy('orden');
    }
}
