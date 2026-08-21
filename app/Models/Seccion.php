<?php

namespace App\Models;

use App\Models\Concerns\GeneraCodigo;
use App\Models\Concerns\RegistraBitacora;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use MongoDB\Laravel\Eloquent\Model;

/**
 * Permiso/pantalla del sistema. Es un catálogo fijo,
 * asignado a los perfiles para controlar accesos.
 */
class Seccion extends Model
{
    use GeneraCodigo;
    use RegistraBitacora;

    public const PREFIJO_CODIGO = 'SEC';

    protected $connection = 'mongodb';
    protected $table = 'seccion';

    protected $fillable = [
        'codigo',
        'nombre',
        'created_at',
    ];

    public function perfiles(): BelongsToMany
    {
        return $this->belongsToMany(Perfil::class, 'perfil_seccion', 'seccion_id', 'perfil_id');
    }
}
