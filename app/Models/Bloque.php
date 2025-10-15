<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bloque extends Model
{
    use HasFactory;

    protected $fillable = ['id', 'nombre', 'proyecto_id'];
    protected $keyType = 'string';
    public $incrementing = false;

    public function proyecto()
    {
        return $this->belongsTo(Proyecto::class);
    }

    public function piezas()
    {
        return $this->hasMany(Pieza::class);
    }
}
