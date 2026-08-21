<?php

namespace App\Models;

use App\Models\Concerns\RegistraBitacora;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\HasApiTokens;
use MongoDB\Laravel\Eloquent\Model;

class Usuario extends Model implements AuthenticatableContract
{
    use Authenticatable;
    use HasApiTokens;
    use HasFactory;
    use RegistraBitacora;

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

        return 'USR-' . str_pad((string) $next, 4, '0', STR_PAD_LEFT);
    }
}
