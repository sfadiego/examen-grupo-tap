<?php

namespace App\Models;

use App\Models\Concerns\GeneraCodigo;
use App\Models\Concerns\RegistraBitacora;
use MongoDB\Laravel\Eloquent\Model;

/**
 * Modelo para un producto
 */
class Producto extends Model
{
    use GeneraCodigo;
    use RegistraBitacora;

    public const PREFIJO_CODIGO = 'PROD';

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

    /**
     * El precio se castea a decimal:2 porque Mongo exige bsonType "decimal" (Decimal128);
     * un float/double normal rompe la validación del schema.
     */
    protected $casts = [
        'precio' => 'decimal:2',
    ];
}
