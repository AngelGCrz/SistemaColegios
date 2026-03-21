<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Seccion extends Model
{
    protected $table = 'secciones';

    protected $fillable = [
        'colegio_id', 'grado_id', 'periodo_id', 'nombre', 'capacidad',
    ];

    public function colegio(): BelongsTo
    {
        return $this->belongsTo(Colegio::class);
    }

    public function grado(): BelongsTo
    {
        return $this->belongsTo(Grado::class);
    }

    public function periodo(): BelongsTo
    {
        return $this->belongsTo(Periodo::class);
    }

    public function cursoSecciones(): HasMany
    {
        return $this->hasMany(CursoSeccion::class);
    }

    public function matriculas(): HasMany
    {
        return $this->hasMany(Matricula::class);
    }

    public function asistencias(): HasMany
    {
        return $this->hasMany(Asistencia::class);
    }

    public function nombreCompleto(): string
    {
        return "{$this->grado->nivel->nombre} - {$this->grado->nombre} \"{$this->nombre}\"";
    }
}
