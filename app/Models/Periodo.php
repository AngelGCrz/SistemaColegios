<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Periodo extends Model
{
    protected $fillable = [
        'colegio_id', 'nombre', 'anio', 'fecha_inicio', 'fecha_fin', 'activo',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'activo' => 'boolean',
    ];

    public function colegio(): BelongsTo
    {
        return $this->belongsTo(Colegio::class);
    }

    public function secciones(): HasMany
    {
        return $this->hasMany(Seccion::class);
    }

    public function matriculas(): HasMany
    {
        return $this->hasMany(Matricula::class);
    }

    public function bimestres(): HasMany
    {
        return $this->hasMany(Bimestre::class);
    }
}
