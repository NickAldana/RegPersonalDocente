<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoPublicacion extends Model
{
    use HasFactory;

    protected $table = 'TipoPublicacion';
    protected $primaryKey = 'IdTipoPublicacion';
    public $timestamps = false;

    protected $fillable = [
        'Descripcion',
        'NombreTipo',
        'Pais'
    ];
}