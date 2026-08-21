<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Producto extends Model
{
    protected $connection = 'mongodb';
    protected $table = 'producto';

    protected $fillable = [
        'codigo',
        'nombre',
        'precio',
        'marca',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'precio' => 'decimal:2',
    ];

    public static function generarCodigo(): string
    {
        $last = static::orderByDesc('created_at')->orderByDesc('_id')->first();

        $next = 1;

        if ($last && preg_match('/(\d+)$/', $last->codigo, $matches)) {
            $next = (int) $matches[1] + 1;
        }

        return 'PROD-' . str_pad((string) $next, 4, '0', STR_PAD_LEFT);
    }
}
