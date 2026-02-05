<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Indicadores extends Model
{
    use HasFactory;

    protected $table = 'Indicadores';
    protected $primaryKey = 'IndicadoresID';
    public $timestamps = false;

    protected $fillable = [
        'Nombreindicador', 
        'Valormaximo', 
        'Valorminimo', 
        'Idcarrera',     // FK lógica
        'Idtipocontrato' // FK lógica
    ];

    protected $casts = [
        'Valormaximo' => 'float',
        'Valorminimo' => 'float',
    ];
}