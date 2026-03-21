<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EntregaTarea extends Model
{
    protected $table = 'entregas_tareas';

    protected $fillable = [
        'colegio_id', 'tarea_id', 'alumno_id', 'contenido',
        'archivo', 'calificacion', 'comentario_docente', 'fecha_entrega',
    ];

    protected $casts = [
        'calificacion' => 'decimal:2',
        'fecha_entrega' => 'datetime',
    ];

    public function colegio(): BelongsTo
    {
        return $this->belongsTo(Colegio::class);
    }

    public function tarea(): BelongsTo
    {
        return $this->belongsTo(Tarea::class);
    }

    public function alumno(): BelongsTo
    {
        return $this->belongsTo(Alumno::class);
    }
}
