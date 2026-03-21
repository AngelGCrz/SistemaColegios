<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tarea extends Model
{
    protected $fillable = [
        'colegio_id', 'curso_seccion_id', 'titulo', 'descripcion',
        'archivo_adjunto', 'fecha_limite', 'puntaje_maximo', 'publicada',
    ];

    protected $casts = [
        'fecha_limite' => 'datetime',
        'puntaje_maximo' => 'decimal:2',
        'publicada' => 'boolean',
    ];

    public function colegio(): BelongsTo
    {
        return $this->belongsTo(Colegio::class);
    }

    public function cursoSeccion(): BelongsTo
    {
        return $this->belongsTo(CursoSeccion::class);
    }

    public function entregas(): HasMany
    {
        return $this->hasMany(EntregaTarea::class);
    }
}
