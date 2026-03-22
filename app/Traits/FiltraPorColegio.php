<?php

namespace App\Traits;

/**
 * Trait para filtrar automáticamente por colegio_id del usuario autenticado.
 * Usar en controladores que necesiten scope multi-tenant.
 */
trait FiltraPorColegio
{
    protected function colegioId(): ?int
    {
        return auth()->user()->colegio_id;
    }

    protected function scopeColegio($query)
    {
        $colegioId = $this->colegioId();

        return $colegioId ? $query->where('colegio_id', $colegioId) : $query;
    }
}
