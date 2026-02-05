<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Personal extends Model
{
    use HasFactory;

    // 1. CONFIGURACIÓN DE TABLA V3.1
    protected $table = 'Personal';
    protected $primaryKey = 'PersonalID';
    public $timestamps = false;

    // 2. MAPEO EXACTO DE COLUMNAS
    protected $fillable = [
        'CodigoItem',        // Item o biométrico
        'Nombrecompleto',   
        'Apellidopaterno',  
        'Apellidomaterno',  
        'Ci',               
        'Correoelectronico',
        'Fechanacimiento',  
        'Genero',           
        'Telelefono',        // Respetamos la doble 'le' del SQL original
        'Fotoperfil',       
        'Añosexperiencia',  
        'Activo',           
        'CargoID',          
        'GradoacademicoID', 
        'TipocontratoID',   
        'UsuarioID'         // FK hacia la tabla Usuario
    ];

    protected $casts = [
        'Fechanacimiento' => 'date',
        'Activo'          => 'boolean',
        'Añosexperiencia' => 'integer',
    ];

    // ========================================================================
    // ACCESORES (LÓGICA DE NEGOCIO PARA VISTAS)
    // ========================================================================

    /**
     * Formato: APELLIDO PATERNO MATERNO, NOMBRES (En Mayúsculas) 
     * Uso: {{ $p->nombre_institucional }}
     */
    public function getNombreInstitucionalAttribute()
    {
        $apellidos = Str::upper("{$this->Apellidopaterno} {$this->Apellidomaterno}");
        $nombres = Str::upper($this->Nombrecompleto);
        return trim("{$apellidos}, {$nombres}");
    }

    /**
     * Nombre para listas simples.
     * Uso: {{ $p->nombre_corto }} -> "Juan Perez"
     */
    public function getNombreCortoAttribute()
    {
        $primerNombre = explode(' ', $this->Nombrecompleto)[0];
        return "{$primerNombre} {$this->Apellidopaterno}";
    }

    // ========================================================================
    // RELACIONES DE CATÁLOGO (PADRES) [cite: 112, 113, 114, 115]
    // ========================================================================

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'UsuarioID', 'UsuarioID');
    }

    public function cargo()
    {
        return $this->belongsTo(Cargo::class, 'CargoID', 'CargoID');
    }

    public function grado()
    {
        return $this->belongsTo(Gradoacademico::class, 'GradoacademicoID', 'GradoacademicoID');
    }

    public function contrato()
    {
        return $this->belongsTo(Tipocontrato::class, 'TipocontratoID', 'TipocontratoID');
    }

    // ========================================================================
    // RELACIONES OPERATIVAS (HIJOS) [cite: 116, 118, 121, 123]
    // ========================================================================

    /**
     * Historial de Formación y Títulos
     */
    public function formaciones()
    {
        return $this->hasMany(Formacion::class, 'PersonalID', 'PersonalID');
    }

    /**
     * Materias con Carga Académica y EVIDENCIA (RutaAutoevaluacion) [cite: 119, 120]
     */
    public function materias()
    {
        return $this->belongsToMany(Materia::class, 'Personalmateria', 'PersonalID', 'MateriaID')
                    ->withPivot('PersonalmateriaID', 'Gestion', 'Periodo', 'RutaAutoevaluacion')
                    ->as('carga'); 
    }

    /**
     * Proyectos de Investigación y Roles [cite: 122]
     */
    public function proyectos()
    {
        return $this->belongsToMany(Proyectoinvestigacion::class, 'Personalproyecto', 'PersonalID', 'ProyectoinvestigacionID')
                    ->withPivot('Rol');
    }

    /**
     * Publicaciones Científicas y Autoria 
     */
    public function publicaciones()
    {
        return $this->belongsToMany(Publicacion::class, 'Personalpublicacion', 'PersonalID', 'PublicacionID')
                    ->withPivot('RolID');
    }

    // ========================================================================
    // SCOPES (FILTROS RÁPIDOS) [cite: 125]
    // ========================================================================
    
    public function scopeActivos($query)
    {
        return $query->where('Activo', 1);
    }

    /**
     * Filtra docentes que pertenezcan a una carrera específica a través de sus materias.
     */
    public function scopeDeCarrera($query, $carreraId)
    {
        return $query->whereHas('materias', function($q) use ($carreraId) {
            $q->where('CarreraID', $carreraId);
        });
    }
}