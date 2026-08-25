<?php

namespace App\Models;

use App\Models\Concerns\GeneraCodigo;
use App\Models\Concerns\RegistraBitacora;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\HasApiTokens;
use MongoDB\Laravel\Eloquent\Model;

/**
 * Representa a un usuario con acceso al sistema. El campo `usuario`
 * es su correo electrónico, usado también para el login vía Sanctum.
 */
class Usuario extends Model implements AuthenticatableContract
{
    use Authenticatable;
    use GeneraCodigo;
    use HasApiTokens;
    use HasFactory;
    use RegistraBitacora;

    public const PREFIJO_CODIGO = 'USR';

    protected $connection = 'mongodb';
    protected $table = 'usuario';

    protected $fillable = [
        'codigo',
        'usuario',
        'nombre',
        'foto',
        'telefono',
        'password',
        'created_at',
    ];

    protected $hidden = [
        'password',
    ];

    protected $appends = [
        'foto_url',
    ];

    // Solo se guarda la ruta relativa de la foto; la URL pública se calcula aquí.
    public function getFotoUrlAttribute(): ?string
    {
        return $this->foto ? Storage::url($this->foto) : null;
    }

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function perfiles(): BelongsToMany
    {
        return $this->belongsToMany(Perfil::class, 'usuario_perfil', 'usuario_id', 'perfil_id');
    }

    /**
     * Secciones a las que tiene acceso
     */
    public function seccionesPermitidas(): Collection
    {
        return $this->load('perfiles.secciones')
            ->perfiles
            ->flatMap(fn ($perfil) => $perfil->secciones)
            ->unique('id');
    }

    public function tieneAcceso(string $seccion): bool
    {
        return $this->seccionesPermitidas()->contains('nombre', $seccion);
    }
}
