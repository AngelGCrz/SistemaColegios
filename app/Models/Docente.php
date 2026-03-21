<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Docente extends Model
{
    protected $fillable = [
        'colegio_id', 'user_id', 'especialidad',
    ];

    public function colegio(): BelongsTo
    {
        return $this->belongsTo(Colegio::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function cursoSecciones(): HasMany
    {
        return $this->hasMany(CursoSeccion::class);
    }
}
