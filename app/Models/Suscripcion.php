<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Suscripcion extends Model
{
    protected $table = 'suscripciones';

    protected $fillable = [
        'colegio_id', 'plan_id', 'estado', 'ciclo',
        'fecha_inicio', 'fecha_fin', 'trial_hasta',
        'monto', 'referencia_pago',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'trial_hasta' => 'date',
        'monto' => 'decimal:2',
    ];

    public function colegio(): BelongsTo
    {
        return $this->belongsTo(Colegio::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function pagos(): HasMany
    {
        return $this->hasMany(PagoSuscripcion::class);
    }

    public function estaVigente(): bool
    {
        if ($this->estado === 'trial') {
            return $this->trial_hasta && $this->trial_hasta->isFuture();
        }

        return $this->estado === 'activa' && $this->fecha_fin->isFuture();
    }

    public function diasRestantes(): int
    {
        $fecha = $this->estado === 'trial' ? $this->trial_hasta : $this->fecha_fin;
        return $fecha ? max(0, now()->diffInDays($fecha, false)) : 0;
    }
}
