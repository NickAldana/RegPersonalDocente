<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CentroFormacion extends Model
{
    use HasFactory;

    protected $table = 'CentroFormacion';
    protected $primaryKey = 'IdCentroFormacion';
    public $timestamps = false;

    protected $fillable = [
        'NombreCentro',
        'Direccion',
        'Pais'
    ];
}