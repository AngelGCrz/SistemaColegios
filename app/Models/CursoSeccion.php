<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CursoSeccion extends Model
{
    protected $table = 'curso_seccion';

    protected $fillable = [
        'colegio_id', 'curso_id', 'seccion_id', 'docente_id',
    ];

    public function colegio(): BelongsTo
    {
        return $this->belongsTo(Colegio::class);
    }

    public function curso(): BelongsTo
    {
        return $this->belongsTo(Curso::class);
    }

    public function seccion(): BelongsTo
    {
        return $this->belongsTo(Seccion::class);
    }

    public function docente(): BelongsTo
    {
        return $this->belongsTo(Docente::class);
    }

    public function notas(): HasMany
    {
        return $this->hasMany(Nota::class);
    }

    public function tareas(): HasMany
    {
        return $this->hasMany(Tarea::class);
    }
}
