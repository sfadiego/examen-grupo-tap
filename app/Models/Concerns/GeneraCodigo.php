<?php

namespace App\Models\Concerns;

/**
 * Genera un código correlativo (ej. PROD-0001) para el modelo que lo use.
 * El modelo debe declarar la constante PREFIJO_CODIGO.
 *
 * @mixin \Illuminate\Database\Eloquent\Model
 */
trait GeneraCodigo
{
    public static function generarCodigo(): string
    {
        // orderByDesc('_id') desempata cuando varios registros comparten el mismo created_at.
        $last = static::orderByDesc('created_at')->orderByDesc('_id')->first();

        $next = 1;

        if ($last && preg_match('/(\d+)$/', $last->codigo, $matches)) {
            $next = (int) $matches[1] + 1;
        }

        return static::PREFIJO_CODIGO . '-' . str_pad((string) $next, 4, '0', STR_PAD_LEFT);
    }
}
