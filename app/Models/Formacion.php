<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Formacion extends Model
{
    use HasFactory;

    protected $table = 'Formacion';
    protected $primaryKey = 'IdFormacion';
    public $timestamps = false;

   protected $fillable = [
    'IdPersonal', 'IdCentroFormacion', 'IdGradoAcademico', 
    'TituloObtenido', 'AñoEstudios', 'RutaArchivo'
];

    public function personal() {
        return $this->belongsTo(Personal::class, 'IdPersonal', 'IdPersonal');
    }

    public function centroFormacion() {
        return $this->belongsTo(CentroFormacion::class, 'IdCentroFormacion', 'IdCentroFormacion');
    }

    public function gradoAcademico() {
        return $this->belongsTo(GradoAcademico::class, 'IdGradoAcademico', 'IdGradoAcademico');
    }
}