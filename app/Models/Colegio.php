<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Colegio extends Model
{
    protected $fillable = [
        'nombre', 'codigo_modular', 'direccion', 'telefono',
        'email', 'logo', 'plan', 'activo', 'fecha_vencimiento',
    ];

    protected $casts = [
        'activo' => 'boolean',
        'fecha_vencimiento' => 'date',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function alumnos(): HasMany
    {
        return $this->hasMany(Alumno::class);
    }

    public function docentes(): HasMany
    {
        return $this->hasMany(Docente::class);
    }

    public function padres(): HasMany
    {
        return $this->hasMany(Padre::class);
    }

    public function periodos(): HasMany
    {
        return $this->hasMany(Periodo::class);
    }

    public function niveles(): HasMany
    {
        return $this->hasMany(Nivel::class);
    }

    public function cursos(): HasMany
    {
        return $this->hasMany(Curso::class);
    }

    public function periodoActivo(): ?Periodo
    {
        return $this->periodos()->where('activo', true)->first();
    }

    public function estaActivo(): bool
    {
        return $this->activo && ($this->fecha_vencimiento === null || $this->fecha_vencimiento->isFuture());
    }
}
