<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Plan extends Model
{
    protected $table = 'planes';

    protected $fillable = [
        'nombre', 'slug', 'precio_mensual', 'precio_anual',
        'max_alumnos', 'caracteristicas', 'activo', 'orden',
    ];

    protected $casts = [
        'precio_mensual' => 'decimal:2',
        'precio_anual' => 'decimal:2',
        'max_alumnos' => 'integer',
        'caracteristicas' => 'array',
        'activo' => 'boolean',
    ];

    public function suscripciones(): HasMany
    {
        return $this->hasMany(Suscripcion::class);
    }

    public function tieneCaracteristica(string $feature): bool
    {
        return in_array($feature, $this->caracteristicas ?? [], true);
    }
}
