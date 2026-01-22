<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Materia extends Model
{
    use HasFactory;

    protected $table = 'Materia';
    protected $primaryKey = 'IdMateria';
    public $timestamps = false;

    protected $fillable = [
        'NombreMateria',
        'IdCarrera', // [NUEVO]
        'Sigla'      // [NUEVO]
    ];

    // Relación: Una materia pertenece a una carrera
    public function carrera()
    {
        return $this->belongsTo(Carrera::class, 'IdCarrera', 'IdCarrera');
    }

    // Relación inversa: Docentes que la dictan
    public function docentes()
    {
        return $this->belongsToMany(Personal::class, 'PersonalMateria', 'IdMateria', 'IdPersonal')
                    ->withPivot('Gestion', 'Periodo');
    }
}