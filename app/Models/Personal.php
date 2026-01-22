<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Personal extends Model
{
    use HasFactory;

    protected $table = 'Personal';
    protected $primaryKey = 'IdPersonal';
    public $timestamps = false;

    protected $fillable = [
    'NombreCompleto', 'ApellidoPaterno', 'ApellidoMaterno', 
    'CI', 'Genero', 'Telefono', 'CorreoElectronico', 
    'IdCargo', 'IdTipoContrato', 'Activo', 
    'AniosExperiencia',
    'IdGradoAcademico'
];

    // --- RELACIONES ---

    // Relación con la tabla de Login (Users)
    public function usuario()
    {
        return $this->hasOne(User::class, 'IdPersonal', 'IdPersonal');
    }

    public function cargo()
    {
        return $this->belongsTo(Cargo::class, 'IdCargo', 'IdCargo');
    }

    public function contrato()
    {
        return $this->belongsTo(TipoContrato::class, 'IdTipoContrato', 'IdTipoContrato');
    }

    public function gradoAcademico()
    {
        return $this->belongsTo(GradoAcademico::class, 'IdGradoAcademico', 'IdGradoAcademico');
    }

    public function formaciones()
    {
        return $this->hasMany(Formacion::class, 'IdPersonal', 'IdPersonal');
    }

    public function materias()
    {
        return $this->belongsToMany(Materia::class, 'PersonalMateria', 'IdPersonal', 'IdMateria')
                    ->withPivot('Gestion', 'Periodo');
    }

    public function carrerasPersonal()
    {
        return $this->hasMany(CarreraPersonal::class, 'IdPersonal', 'IdPersonal');
    }

    public function publicaciones()
    {
        return $this->hasMany(Publicaciones::class, 'IdPersonal', 'IdPersonal');
    }

    // --- SCOPES ---
    public function scopeActivos($query)
    {
        return $query->where('Activo', 1);
    }
    
    // Relación directa para acceder a las carreras del docente/jefe
    public function carreras()
    {
        return $this->belongsToMany(
            Carrera::class, 
            'CarreraPersonal', // Tabla intermedia
            'IdPersonal',      // FK de este modelo
            'IdCarrera'        // FK del otro modelo
        )->withPivot('Gestion');
    }
}