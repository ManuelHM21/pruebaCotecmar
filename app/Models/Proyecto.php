<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proyecto extends Model
{
    use HasFactory;

    protected $fillable = ['id', 'nombre'];
    protected $keyType = 'string';
    public $incrementing = false;

    public function bloques()
    {
        return $this->hasMany(Bloque::class);
    }

    public function piezas()
    {
        return $this->hasManyThrough(Pieza::class, Bloque::class);
    }
}
