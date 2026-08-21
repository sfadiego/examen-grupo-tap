<?php

namespace App\Models;

use App\Models\Concerns\GeneraCodigo;
use App\Models\Concerns\RegistraBitacora;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use MongoDB\Laravel\Eloquent\Model;

/**
 * Rol de acceso. Agrupa varias secciones y se asigna a uno o más usuarios.
 */
class Perfil extends Model
{
    use GeneraCodigo;
    use RegistraBitacora;

    public const PREFIJO_CODIGO = 'PERF';

    protected $connection = 'mongodb';
    protected $table = 'perfil';

    protected $fillable = [
        'codigo',
        'nombre',
        'created_at',
    ];

    public function usuarios(): BelongsToMany
    {
        return $this->belongsToMany(Usuario::class, 'usuario_perfil', 'perfil_id', 'usuario_id');
    }

    public function secciones(): BelongsToMany
    {
        return $this->belongsToMany(Seccion::class, 'perfil_seccion', 'perfil_id', 'seccion_id');
    }
}
