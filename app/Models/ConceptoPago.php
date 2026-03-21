<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConceptoPago extends Model
{
    protected $table = 'conceptos_pago';

    protected $fillable = [
        'colegio_id', 'nombre', 'monto', 'activo',
    ];

    protected $casts = [
        'monto' => 'decimal:2',
        'activo' => 'boolean',
    ];

    public function colegio(): BelongsTo
    {
        return $this->belongsTo(Colegio::class);
    }
}
