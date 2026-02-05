<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Formacion extends Model
{
    use HasFactory;

    // 1. Configuración V3.1
    protected $table = 'Formacion';
    protected $primaryKey = 'FormacionID';
    public $timestamps = false;

    // 2. Campos
    protected $fillable = [
        'NombreProfesion',   // Ej: "Ingeniero en Sistemas"
        'Tituloobtenido',    // Ej: "Licenciatura"
        'Añosestudios',      // Ej: 5
        'RutaArchivo',       // VITAL: Path al PDF escaneado
        'CentroformacionID', // Dónde estudió
        'PersonalID',        // Quién es
        'GradoacademicoID'   // Qué nivel es (PhD, MSc)
    ];

    // ========================================================================
    // RELACIONES
    // ========================================================================

    public function personal()
    {
        return $this->belongsTo(Personal::class, 'PersonalID', 'PersonalID');
    }

    public function centro()
    {
        return $this->belongsTo(Centroformacion::class, 'CentroformacionID', 'CentroformacionID');
    }

    public function grado()
    {
        return $this->belongsTo(Gradoacademico::class, 'GradoacademicoID', 'GradoacademicoID');
    }

    // ========================================================================
    // HELPER (FULLSTACK)
    // ========================================================================
    // Verifica si hay evidencia digital cargada
    public function getTieneEvidenciaAttribute()
    {
        return !empty($this->RutaArchivo);
    }
}