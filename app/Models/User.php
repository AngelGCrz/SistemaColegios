<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'colegio_id', 'nombre', 'apellidos', 'email', 'password',
        'rol', 'telefono', 'dni', 'activo',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'activo' => 'boolean',
        ];
    }

    // --- Relaciones ---

    public function colegio(): BelongsTo
    {
        return $this->belongsTo(Colegio::class);
    }

    public function alumno(): HasOne
    {
        return $this->hasOne(Alumno::class);
    }

    public function docente(): HasOne
    {
        return $this->hasOne(Docente::class);
    }

    public function padre(): HasOne
    {
        return $this->hasOne(Padre::class);
    }

    // --- Helpers de rol ---

    public function esSuperAdmin(): bool
    {
        return $this->rol === 'superadmin';
    }

    public function esAdmin(): bool
    {
        return $this->rol === 'admin';
    }

    public function esDocente(): bool
    {
        return $this->rol === 'docente';
    }

    public function esAlumno(): bool
    {
        return $this->rol === 'alumno';
    }

    public function esPadre(): bool
    {
        return $this->rol === 'padre';
    }

    public function nombreCompleto(): string
    {
        return "{$this->nombre} {$this->apellidos}";
    }

    // --- Scopes ---

    public function scopeDelColegio($query, int $colegioId)
    {
        return $query->where('colegio_id', $colegioId);
    }

    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    public function scopeRol($query, string $rol)
    {
        return $query->where('rol', $rol);
    }
}
