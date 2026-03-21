<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Matricula extends Model
{
    protected $fillable = [
        'colegio_id', 'alumno_id', 'seccion_id', 'periodo_id',
        'estado', 'fecha_matricula',
    ];

    protected $casts = [
        'fecha_matricula' => 'date',
    ];

    public function colegio(): BelongsTo
    {
        return $this->belongsTo(Colegio::class);
    }

    public function alumno(): BelongsTo
    {
        return $this->belongsTo(Alumno::class);
    }

    public function seccion(): BelongsTo
    {
        return $this->belongsTo(Seccion::class);
    }

    public function periodo(): BelongsTo
    {
        return $this->belongsTo(Periodo::class);
    }

    public function notas(): HasMany
    {
        return $this->hasMany(Nota::class);
    }

    public function asistencias(): HasMany
    {
        return $this->hasMany(Asistencia::class);
    }
}
