<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Nota extends Model
{
    protected $fillable = [
        'colegio_id', 'matricula_id', 'curso_seccion_id',
        'bimestre_id', 'nota', 'nota_letra', 'observacion',
    ];

    protected $casts = [
        'nota' => 'decimal:2',
    ];

    public function colegio(): BelongsTo
    {
        return $this->belongsTo(Colegio::class);
    }

    public function matricula(): BelongsTo
    {
        return $this->belongsTo(Matricula::class);
    }

    public function cursoSeccion(): BelongsTo
    {
        return $this->belongsTo(CursoSeccion::class);
    }

    public function bimestre(): BelongsTo
    {
        return $this->belongsTo(Bimestre::class);
    }
}
