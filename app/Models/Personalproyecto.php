<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class Personalproyecto extends Pivot
{
    protected $table = 'Personalproyecto';
    protected $primaryKey = 'PersonalproyectoID';
    public $timestamps = false;
    public $incrementing = true; 

    protected $fillable = [
        'PersonalID',
        'ProyectoinvestigacionID',
        
        // --- NUEVOS CAMPOS V3.3 ---
        'Rol', // Ej: "Asesor Metodológico/responsable de investigacion pasante resposnable de pasantia estudiantes investigacion etc"
        'EsResponsable',    // 1 (Sí) o 0 (No)
        'FechaInicio',
        'FechaFin'
    ];

    // Para que Laravel maneje las fechas y el booleano automáticamente
   // En tu modelo Personalproyecto.php
protected $casts = [
    'EsResponsable' => 'boolean',
    'FechaInicio'   => 'date:Y-m-d', // Fuerza el formato
    'FechaFin'      => 'date:Y-m-d', 
];

    public function personal() { return $this->belongsTo(Personal::class, 'PersonalID'); }
    public function proyecto() { return $this->belongsTo(Proyectoinvestigacion::class, 'ProyectoinvestigacionID'); }
}