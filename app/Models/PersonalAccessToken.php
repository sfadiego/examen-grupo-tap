<?php

namespace App\Models;

use Laravel\Sanctum\PersonalAccessToken as SanctumPersonalAccessToken;

/**
 * Sobrescribe el modelo por defecto de Sanctum para forzar la conexión
 * 'sqlite'. Sin esto, Eloquent hereda la conexión 'mongodb' del modelo
 * que crea el token (ej. Usuario), y falla porque esta tabla usa
 * relaciones polimórficas que requieren SQL real.
 */
class PersonalAccessToken extends SanctumPersonalAccessToken
{
    protected $connection = 'sqlite';
}
