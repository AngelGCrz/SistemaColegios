<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Alumno extends Model
{
    protected $fillable = [
        'colegio_id', 'user_id', 'codigo_alumno',
        'fecha_nacimiento', 'genero', 'direccion', 'observaciones',
    ];

    protected $casts = [
        'fecha_nacimiento' => 'date',
    ];

    public function colegio(): BelongsTo
    {
        return $this->belongsTo(Colegio::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function padres(): BelongsToMany
    {
        return $this->belongsToMany(Padre::class, 'alumno_padre')
                    ->withPivot('parentesco');
    }

    public function matriculas(): HasMany
    {
        return $this->hasMany(Matricula::class);
    }

    public function pagos(): HasMany
    {
        return $this->hasMany(Pago::class);
    }

    public function entregasTareas(): HasMany
    {
        return $this->hasMany(EntregaTarea::class);
    }

    public function matriculaActiva()
    {
        // Use loaded relation if available to avoid N+1
        if ($this->relationLoaded('matriculas')) {
            return $this->matriculas
                ->where('estado', 'activa')
                ->filter(fn ($m) => $m->relationLoaded('periodo') ? $m->periodo?->activo : true)
                ->first();
        }

        return $this->matriculas()
            ->where('estado', 'activa')
            ->whereHas('periodo', fn ($q) => $q->where('activo', true))
            ->first();
    }
}
