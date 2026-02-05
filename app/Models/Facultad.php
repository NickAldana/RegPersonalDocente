<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Facultad extends Model
{
    use HasFactory;

    protected $table = 'Facultad';
    protected $primaryKey = 'FacultadID';
    public $timestamps = false;

    protected $fillable = [
        'CodigoFacultad', // Ej: FIN, FICH (Mantenido en V3.1)
        'Nombrefacultad'
    ];

    // Relaciones (Hacia el futuro Grupo 3)
    public function carreras()
    {
        return $this->hasMany(Carrera::class, 'FacultadID');
    }

    public function lineasInvestigacion()
    {
        return $this->hasMany(Lineainvestigacion::class, 'FacultadID');
    }
}