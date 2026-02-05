<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cargo extends Model
{
    use HasFactory;

    // 1. Configuración de Tabla V3.1
    protected $table = 'Cargo';
    protected $primaryKey = 'CargoID';
    public $timestamps = false;

    // 2. CONSTANTES DE NIVEL (Para no usar números "mágicos" en tu código)
    // Úsalas en tus Controladores así: Cargo::NIVEL_AUTORIDAD
    const NIVEL_AUTORIDAD     = 1; // Rector, Vicerrector, Decano
    const NIVEL_DIRECTIVO     = 2; // Directores de Carrera / Jefes Dpto
    const NIVEL_DOCENTE       = 3; // Docentes Tiempo Completo / Horario
    const NIVEL_ADMINISTRATIVO= 4; // Secretarias, Asistentes

    // 3. Limpieza: Quitamos 'Idcargo' porque en el Script V3.1 lo borramos.
    protected $fillable = [
        'Nombrecargo', 
        'nivel_jerarquico' 
    ];

    protected $casts = [
        'nivel_jerarquico' => 'integer',
    ];

    // --- RELACIONES ---

    /**
     * Relación con Personal: ¿Quiénes ocupan este cargo?
     */
    public function personal()
    {
        return $this->hasMany(Personal::class, 'CargoID', 'CargoID');
    }

    /**
     * Relación con Permisos (Seguridad RBAC)
     * Tabla intermedia: Cargopermiso
     */
    public function permisos()
    {
        return $this->belongsToMany(
            Permiso::class, 
            'Cargopermiso', // Tabla pivot V3.1
            'CargoID',      // FK local
            'PermisosID'    // FK destino
        );
    }

    // --- ACCESORES DE ANALÍTICA ---

    /**
     * Devuelve un texto legible del nivel para las Vistas (Blade)
     * Uso: {{ $cargo->nivel_legible }}
     */
    public function getNivelLegibleAttribute()
    {
        return match($this->nivel_jerarquico) {
            self::NIVEL_AUTORIDAD      => 'Alta Dirección',
            self::NIVEL_DIRECTIVO      => 'Dirección / Jefatura',
            self::NIVEL_DOCENTE        => 'Plantel Docente',
            self::NIVEL_ADMINISTRATIVO => 'Personal Administrativo',
            default                    => 'Sin Clasificar',
        };
    }
}