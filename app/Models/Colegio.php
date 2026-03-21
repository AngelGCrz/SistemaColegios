<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Colegio extends Model
{
    protected $fillable = [
        'nombre', 'codigo_modular', 'direccion', 'telefono',
        'email', 'logo', 'subdominio', 'plan', 'activo', 'fecha_vencimiento',
        'contacto_nombre', 'contacto_telefono', 'contacto_email',
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

    public function suscripciones(): HasMany
    {
        return $this->hasMany(Suscripcion::class);
    }

    public function suscripcionActiva(): HasOne
    {
        return $this->hasOne(Suscripcion::class)
            ->whereIn('estado', ['activa', 'trial'])
            ->latest('id');
    }

    public function estaActivo(): bool
    {
        return $this->activo && ($this->fecha_vencimiento === null || $this->fecha_vencimiento->isFuture());
    }

    public function alumnosCount(): int
    {
        return $this->alumnos()->count();
    }
}
