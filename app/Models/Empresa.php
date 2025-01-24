<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Empresa extends Model
{
    use HasFactory;

    protected $table = 'empresa';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'id',
        'nombre',
        'titular',
        'actividad',
        'habilitado',
    ];

    public function agencias()
    {
        return $this->hasMany(Agencia::class, 'empresa_id');
    }
}
