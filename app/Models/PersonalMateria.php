<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PersonalMateria extends Model
{
    use HasFactory;

    protected $table = 'PersonalMateria';
    protected $primaryKey = 'IdPersonalMateria';
    public $timestamps = false;

    protected $fillable = [
        'IdPersonal', 
        'IdMateria', 
        // 'IdCargo' <-- ELIMINADO SEGÚN TU NUEVO SQL
        'Gestion', 
        'Periodo'
    ];

    public function personal() { return $this->belongsTo(Personal::class, 'IdPersonal', 'IdPersonal'); }
    public function materia() { return $this->belongsTo(Materia::class, 'IdMateria', 'IdMateria'); }
}