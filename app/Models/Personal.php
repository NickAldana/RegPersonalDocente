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
    // RELACIONES DE CATÁLOGO (PADRES)
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
    // RELACIONES OPERATIVAS (HIJOS)
    // ========================================================================

    /**
     * Historial de Formación y Títulos
     */
    public function formaciones()
    {
        return $this->hasMany(Formacion::class, 'PersonalID', 'PersonalID');
    }

    /**
     * Materias con Carga Académica y EVIDENCIA (RutaAutoevaluacion)
     * AJUSTADO: Se agregaron los campos extra 'Grupo' y 'Modalidad' al pivot.
     */
    public function materias()
    {
        return $this->belongsToMany(Materia::class, 'Personalmateria', 'PersonalID', 'MateriaID')
                    ->withPivot('PersonalmateriaID', 'Gestion', 'Periodo', 'Grupo', 'Modalidad', 'RutaAutoevaluacion')
                    ->as('carga'); 
    }

    /**
     * Proyectos de Investigación y Roles
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
    // SCOPES (FILTROS RÁPIDOS)
    // ========================================================================
    
    public function scopeActivos($query)
    {
        return $query->where('Activo', 1);
    }

    /**
     * Filtra docentes que pertenezcan a una carrera específica a través de sus materias.
     * CORREGIDO: Ahora navega la relación N:M entre Materia y Carrera.
     */
    public function scopeDeCarrera($query, $carreraId)
    {
        return $query->whereHas('materias', function($qMateria) use ($carreraId) {
            // Entramos a la relación 'carreras' del modelo Materia
            $qMateria->whereHas('carreras', function($qCarrera) use ($carreraId) {
                // Especificamos la tabla para evitar error de "Ambiguous column"
                $qCarrera->where('Carrera.CarreraID', $carreraId);
            });
        });
    }
    /**
     * Calcula el nivel de Seniority basado en la experiencia.
     * Uso: <span class="badge {{ $p->seniority_color }}">{{ $p->seniority_label }}</span>
     */
    public function getSeniorityLabelAttribute()
    {
        $anios = $this->Añosexperiencia ?? 0;
        
        if ($anios >= 15) return 'MÁSTER';
        if ($anios >= 10) return 'SENIOR';
        if ($anios >= 5)  return 'JUNIOR';
        return 'INICIAL';
    }

    public function getSeniorityColorAttribute()
    {
        $anios = $this->Añosexperiencia ?? 0;
        
        if ($anios >= 15) return 'bg-purple-100 text-purple-700 border-purple-200'; // Morado Épico
        if ($anios >= 10) return 'bg-upds-gold text-white border-warning'; // Dorado Legendario
        if ($anios >= 5)  return 'bg-blue-100 text-blue-700 border-blue-200'; // Azul Raro
        return 'bg-slate-100 text-slate-500 border-slate-200'; // Gris Común
    }
}