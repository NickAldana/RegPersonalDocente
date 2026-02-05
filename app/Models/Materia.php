<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Materia extends Model
{
    use HasFactory;

    // 1. Configuración V3.1
    protected $table = 'Materia';
    protected $primaryKey = 'MateriaID';
    public $timestamps = false;

    // 2. Campos (Sigla es el protagonista aquí)
    protected $fillable = [
        'Sigla',          // Ej: INF110
        'Nombremateria',  // Ej: Estructuras de Datos I
        'CarreraID'       // FK
    ];

    // ========================================================================
    // RELACIONES
    // ========================================================================

    /**
     * Jerarquía: Pertenece a una Carrera.
     */
    public function carrera()
    {
        return $this->belongsTo(Carrera::class, 'CarreraID', 'CarreraID');
    }

    /**
     * Carga Horaria: ¿Quién dicta esta materia?
     * IMPORTANTE: Incluimos 'RutaAutoevaluacion' para poder descargar el PDF desde aquí.
     */
    public function docentes()
    {
        return $this->belongsToMany(Personal::class, 'Personalmateria', 'MateriaID', 'PersonalID')
                    ->withPivot('PersonalmateriaID', 'Gestion', 'Periodo', 'RutaAutoevaluacion')
                    ->as('asignacion'); // Alias para usar en Blade: $materia->asignacion->Gestion
    }

    // ========================================================================
    // SCOPES (BÚSQUEDAS RÁPIDAS)
    // ========================================================================

    public function scopePorSigla($query, $sigla)
    {
        return $query->where('Sigla', 'LIKE', "%$sigla%");
    }
}