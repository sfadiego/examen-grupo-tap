<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Bitacora extends Model
{
    protected $connection = 'mongodb';
    protected $table = 'bitacora';

    protected $fillable = [
        'modelo',
        'modelo_id',
        'datos_anteriores',
        'datos_nuevos',
        'usuario_id',
        'created_at',
    ];
}
