<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tipocontrato extends Model
{
    use HasFactory;

    protected $table = 'Tipocontrato';
    protected $primaryKey = 'TipocontratoID';
    public $timestamps = false;

    protected $fillable = [
        'Nombrecontrato', // Ej: Tiempo Completo, Invitado, Horario
        'Descripcion',
        'IndicadoresID'
    ];

    /**
     * Relación: Un contrato puede estar sujeto a ciertos Indicadores de desempeño.
     */
    public function indicadores()
    {
        return $this->belongsTo(Indicadores::class, 'IndicadoresID');
    }

    /**
     * Relación: El personal que tiene este tipo de contrato.
     */
    public function personales()
    {
        return $this->hasMany(Personal::class, 'TipocontratoID');
    }
}