<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Grado extends Model
{
    protected $fillable = [
        'colegio_id', 'nivel_id', 'nombre', 'orden',
    ];

    public function colegio(): BelongsTo
    {
        return $this->belongsTo(Colegio::class);
    }

    public function nivel(): BelongsTo
    {
        return $this->belongsTo(Nivel::class);
    }

    public function secciones(): HasMany
    {
        return $this->hasMany(Seccion::class);
    }
}
