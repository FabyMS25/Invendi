<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Agencia extends Model
{
    use HasFactory;

    protected $table = 'agencia';
    protected $primaryKey = 'id';
    public $timestamps = true;
    protected $fillable = [
        'empresa_id',
        'nombre',
        'direccion',
        'telefonos',
        'geolocalizacion',
        'correo_agencia',
        'habilitado',
    ];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }
}
