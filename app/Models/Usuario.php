<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Contracts\Auth\MustVerifyEmail;

class Usuario extends Authenticatable
{
    use /* HasApiTokens, */ HasFactory, Notifiable;

    protected $table = 'usuario';
    protected $primaryKey = 'id';
    public $timestamps = true;
    protected $fillable = [
        'nombre',
        'email',
        'celular',
        'whatsapp',
        'nickname',
        'password',
        'habilitado',
        'ayuda',
        'tipo_usuario_id',
        'ultima_conexion',
    ];

    /**
     * The attributes that should be hidden for serialization.
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];
    /**
     * Get the attributes that should be cast.
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'ultima_conexion' => 'datetime',
            'password' => 'hashed',
            'habilitado' => 'boolean',
            // 'ayuda' => 'boolean',
            'tipo_usuario_id' => 'integer',
        ];
    }

    public function menuUsuario()
    {
        return $this->hasMany(MenuUsuario::class, 'usuario_id');
    }

    /**
     * Update the user's last connection timestamp.
     */
    public function updateLastConnection()
    {
        $this->ultima_conexion = now();
        $this->save();
    }

    /**
     * Scope a query to only include active users.
     */
    public function scopeActive($query)
    {
        return $query->where('habilitado', true);
    }

    /**
     * Check if the user is active.
     */
    public function isActive(): bool
    {
        return $this->habilitado;
    }

    /**
     * Check if user needs help.
     */
    public function needsHelp(): bool
    {
        return $this->ayuda;
    }
}
