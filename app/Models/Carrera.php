<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Carrera extends Model {
    use HasFactory;
    protected $table = 'Carrera';
    protected $primaryKey = 'IdCarrera';
    public $timestamps = false;
    protected $fillable = ['IdFacultad', 'NombreCarrera'];

    // Relación: Una carrera pertenece a una facultad
    public function facultad() {
        return $this->belongsTo(Facultad::class, 'IdFacultad', 'IdFacultad');
    }
    // Relación: Una carrera tiene muchas materias
    public function materias() {
        return $this->hasMany(Materia::class, 'IdCarrera', 'IdCarrera');
    }
    }
