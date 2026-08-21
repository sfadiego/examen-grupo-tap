<?php

namespace App\Models;

use App\Models\Concerns\RegistraBitacora;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use MongoDB\Laravel\Eloquent\Model;

class Seccion extends Model
{
    use RegistraBitacora;

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

    public static function generarCodigo(): string
    {
        // ultimo registro creado, fecha y por id
        $last = static::orderByDesc('created_at')
            ->orderByDesc('_id')
            ->first();

        $next = 1;

        if ($last && preg_match('/(\d+)$/', $last->codigo, $matches)) {
            $next = (int) $matches[1] + 1;
        }

        return 'SEC-' . str_pad((string) $next, 4, '0', STR_PAD_LEFT);
    }
}
