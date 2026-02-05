<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Proyectoinvestigacion extends Model
{
    use HasFactory;

    protected $table = 'Proyectoinvestigacion';
    protected $primaryKey = 'ProyectoinvestigacionID';
    
    // DESACTIVADO: Evita el error de columna inexistente en SQL Server
    public $timestamps = false; 

    protected $fillable = [
        'CodigoProyecto',
        'Nombreproyecto',
        'Fechainicio',
        'Fechafinalizacion',
        'Estado',
        'CarreraID',
        'LineainvestigacionID'
    ];

    // Mantenemos los casts para que Eloquent maneje los objetos Carbon automáticamente
    protected $casts = [
        'Fechainicio'       => 'date:Y-m-d',
        'Fechafinalizacion' => 'date:Y-m-d',
    ];

    // ========================================================================
    // RELACIONES
    // ========================================================================

    public function carrera()
    {
        return $this->belongsTo(Carrera::class, 'CarreraID', 'CarreraID');
    }

    public function linea()
    {
        return $this->belongsTo(Lineainvestigacion::class, 'LineainvestigacionID', 'LineainvestigacionID');
    }

    public function equipo()
    {
        return $this->belongsToMany(Personal::class, 'Personalproyecto', 'ProyectoinvestigacionID', 'PersonalID')
                    ->withPivot('Rol', 'EsResponsable', 'FechaInicio', 'FechaFin')
                    ->using(Personalproyecto::class);
    }

    public function publicaciones()
    {
        return $this->hasMany(Publicacion::class, 'ProyectoinvestigacionID', 'ProyectoinvestigacionID');
    }
}