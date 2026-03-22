<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RecursoDigital extends Model
{
    protected $table = 'recursos_digitales';

    protected $fillable = [
        'colegio_id', 'user_id', 'titulo', 'descripcion',
        'tipo', 'archivo_path', 'archivo_nombre', 'url_externa',
        'materia', 'nivel', 'publico', 'descargas',
    ];

    protected $casts = [
        'publico' => 'boolean',
        'descargas' => 'integer',
    ];

    public function colegio(): BelongsTo
    {
        return $this->belongsTo(Colegio::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
