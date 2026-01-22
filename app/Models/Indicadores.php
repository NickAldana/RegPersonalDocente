<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Indicadores extends Model
{
    use HasFactory;

    protected $table = 'Indicadores';
    protected $primaryKey = 'IdIndicador';
    public $timestamps = false;

    protected $fillable = [
        'NombreIndicador',
        'IdCarrera',
        'IdTipoContrato',
        'ValorMinimo',
        'ValorOptimo'
    ];
}