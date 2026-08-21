<?php

namespace App\Models;

use App\Models\Concerns\RegistraBitacora;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use MongoDB\Laravel\Eloquent\Model;

class Perfil extends Model
{
    use RegistraBitacora;

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

    public static function generarCodigo(): string
    {
        $last = static::orderByDesc('created_at')->orderByDesc('_id')->first();

        $next = 1;

        if ($last && preg_match('/(\d+)$/', $last->codigo, $matches)) {
            $next = (int) $matches[1] + 1;
        }

        return 'PERF-' . str_pad((string) $next, 4, '0', STR_PAD_LEFT);
    }
}
