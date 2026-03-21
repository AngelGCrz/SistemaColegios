<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PagoSuscripcion extends Model
{
    protected $table = 'pagos_suscripcion';

    protected $fillable = [
        'colegio_id', 'suscripcion_id', 'monto', 'moneda',
        'estado', 'metodo_pago', 'referencia_externa',
        'metadata', 'pagado_en',
    ];

    protected $casts = [
        'monto' => 'decimal:2',
        'metadata' => 'array',
        'pagado_en' => 'datetime',
    ];

    public function colegio(): BelongsTo
    {
        return $this->belongsTo(Colegio::class);
    }

    public function suscripcion(): BelongsTo
    {
        return $this->belongsTo(Suscripcion::class);
    }
}
