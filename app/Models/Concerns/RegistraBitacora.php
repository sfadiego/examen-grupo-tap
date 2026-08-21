<?php

namespace App\Models\Concerns;

use App\Models\Bitacora;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use MongoDB\BSON\ObjectId;

/**
 * @mixin \Illuminate\Database\Eloquent\Model
 */
trait RegistraBitacora
{
    protected array $datosAntesDeActualizar = [];

    // Eloquent llama automáticamente boot+ NombreTrait al iniciar el modelo.
    protected static function bootRegistraBitacora(): void
    {
        static::updating(function ($modelo) {
            $modelo->datosAntesDeActualizar = Arr::except($modelo->getOriginal(), $modelo->getHidden());
        });

        static::updated(function ($modelo) {
            Bitacora::create([
                'modelo' => class_basename($modelo),
                'modelo_id' => new ObjectId($modelo->getKey()),
                'datos_anteriores' => $modelo->datosAntesDeActualizar,
                'datos_nuevos' => Arr::except($modelo->getAttributes(), $modelo->getHidden()),
                'usuario_id' => Auth::id() ? new ObjectId(Auth::id()) : null,
            ]);
        });

        static::created(function ($modelo) {
            Bitacora::create([
                'modelo' => class_basename($modelo),
                'modelo_id' => new ObjectId($modelo->getKey()),
                'datos_anteriores' => (object) [],
                'datos_nuevos' => Arr::except($modelo->getAttributes(), $modelo->getHidden()),
                'usuario_id' => Auth::id() ? new ObjectId(Auth::id()) : null,
            ]);
        });
    }
}
