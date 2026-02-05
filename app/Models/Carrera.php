<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Carrera extends Model
{
    use HasFactory;

    protected $table = 'Carrera';
    protected $primaryKey = 'CarreraID';
    public $timestamps = false;

    protected $fillable = [
        'CodigoCarrera', // Ej: 187-4 (V3.1)
        'Nombrecarrera', 
        'FacultadID', 
        'IndicadoresID'
    ];

    /**
     * Relación: Una carrera pertenece a una Facultad.
     * Uso: $carrera->facultad->Nombrefacultad
     */
    public function facultad()
    {
        return $this->belongsTo(Facultad::class, 'FacultadID');
    }

    /**
     * Relación: Indicadores de calidad asignados a la carrera.
     */
    public function indicadores()
    {
        return $this->belongsTo(Indicadores::class, 'IndicadoresID');
    }

    /**
     * Relación: Materias que pertenecen a esta carrera.
     */
    public function materias()
    {
        return $this->hasMany(Materia::class, 'CarreraID');
    }

    /**
     * Relación: Proyectos de investigación nacidos en esta carrera.
     */
    public function proyectos()
    {
        return $this->hasMany(Proyectoinvestigacion::class, 'CarreraID');
    }
}