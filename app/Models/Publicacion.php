<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Publicacion extends Model
{
    use HasFactory;

    protected $table = 'Publicacion';
    protected $primaryKey = 'PublicacionID';
    public $timestamps = false;

protected $fillable = [
        'Nombrepublicacion',
        'Fechapublicacion',
        'MediopublicacionID',
        'TipopublicacionID',
        'UrlPublicacion',
        'ProyectoinvestigacionID',
        'LineainvestigacionID',
        'RutaArchivo'     // Link externo
    ];

    protected $casts = [
        'Fechapublicacion' => 'date',
    ];

    // ========================================================================
    // RELACIONES
    // ========================================================================

    public function medio()
    {
        return $this->belongsTo(Mediopublicacion::class, 'MediopublicacionID', 'MediopublicacionID');
    }

    public function tipo()
    {
        return $this->belongsTo(Tipopublicacion::class, 'TipopublicacionID', 'TipopublicacionID');
    }

    public function proyecto()
    {
        return $this->belongsTo(Proyectoinvestigacion::class, 'ProyectoinvestigacionID', 'ProyectoinvestigacionID');
    }

    // NUEVO: Relación para saber de qué tema trata esta publicación
    public function linea()
    {
        return $this->belongsTo(Lineainvestigacion::class, 'LineainvestigacionID', 'LineainvestigacionID');
    }

    public function autores()
    {
        return $this->belongsToMany(Personal::class, 'Personalpublicacion', 'PublicacionID', 'PersonalID')
                    ->withPivot('RolID');
    }
}