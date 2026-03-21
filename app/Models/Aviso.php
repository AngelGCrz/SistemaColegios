<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Aviso extends Model
{
    protected $fillable = [
        'colegio_id', 'user_id', 'titulo', 'contenido',
        'destinatario', 'seccion_id', 'publicado',
    ];

    protected $casts = [
        'publicado' => 'boolean',
    ];

    public function colegio(): BelongsTo
    {
        return $this->belongsTo(Colegio::class);
    }

    public function autor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function seccion(): BelongsTo
    {
        return $this->belongsTo(Seccion::class);
    }
}
