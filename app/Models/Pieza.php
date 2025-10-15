<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pieza extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'id', 'nombre', 'peso_teorico', 'bloque_id', 'peso_real', 'estado', 'fecha_registro', 'registrado_por'
    ];

    public function bloque()
    {
        return $this->belongsTo(Bloque::class, 'bloque_id');
    }
}
