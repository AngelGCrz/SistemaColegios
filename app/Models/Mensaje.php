<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Mensaje extends Model
{
    protected $fillable = [
        'colegio_id', 'remitente_id', 'destinatario_id',
        'asunto', 'contenido', 'leido', 'leido_at',
    ];

    protected $casts = [
        'leido' => 'boolean',
        'leido_at' => 'datetime',
    ];

    public function colegio(): BelongsTo
    {
        return $this->belongsTo(Colegio::class);
    }

    public function remitente(): BelongsTo
    {
        return $this->belongsTo(User::class, 'remitente_id');
    }

    public function destinatario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'destinatario_id');
    }

    public function marcarLeido(): void
    {
        $this->update(['leido' => true, 'leido_at' => now()]);
    }
}
