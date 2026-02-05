<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lineainvestigacion extends Model
{
    use HasFactory;

    protected $table = 'Lineainvestigacion';
    protected $primaryKey = 'LineainvestigacionID';
    public $timestamps = false;

    protected $fillable = [
        'Nombrelineainvestigacion', 
        'Descripcion', 
        'FacultadID',
        'EsTransversal'
    ];

    protected $casts = [
        'EsTransversal' => 'boolean',
    ];

    /**
     * Relación: La Facultad dueña de la línea.
     */
    public function facultad()
    {
        return $this->belongsTo(Facultad::class, 'FacultadID', 'FacultadID');
    }

    /**
     * INTENCIÓN: Proyectos que prometieron investigar este tema.
     */
    public function proyectos()
    {
        return $this->hasMany(Proyectoinvestigacion::class, 'LineainvestigacionID', 'LineainvestigacionID');
    }

    /**
     * RESULTADO: Publicaciones reales generadas en este tema.
     */
    public function publicaciones()
    {
        return $this->hasMany(Publicacion::class, 'LineainvestigacionID', 'LineainvestigacionID');
    }
}