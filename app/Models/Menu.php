<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory;

    protected $table = 'menu';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'nombre',
        'url',
        'aplicacion',
        'modulo',
        'orden_modulo',
        'agrupador',
        'habilitado',
    ];

    public function menuUsuario()
    {
        return $this->hasMany(MenuUsuario::class, 'menu_id');
    }

    public function rolesMenu()
    {
        return $this->hasMany(RolMenu::class, 'menu_id');
    }
}
