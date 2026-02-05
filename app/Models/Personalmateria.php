<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class Personalmateria extends Pivot
{
    // Configuración especial para Pivote con ID
    protected $table = 'Personalmateria';
    protected $primaryKey = 'PersonalmateriaID';
    public $timestamps = false;
    public $incrementing = true; // IMPORTANTE: Porque tiene ID propio identity

    protected $fillable = [
        'Gestion',            // 2025
        'Periodo',            // 1
        'RutaAutoevaluacion', // El archivo crítico
        'PersonalID',
        'MateriaID'
    ];

    // Relaciones (para poder acceder a los padres desde la pivote)
    public function personal() { return $this->belongsTo(Personal::class, 'PersonalID'); }
    public function materia() { return $this->belongsTo(Materia::class, 'MateriaID'); }
}