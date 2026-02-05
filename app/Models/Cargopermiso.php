<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class Cargopermiso extends Pivot
{
    protected $table = 'Cargopermiso';
    protected $primaryKey = 'CargopermisoID';
    public $timestamps = false;
    public $incrementing = true; // Al tener una PK Identity propia

    protected $fillable = [
        'CargoID', 
        'PermisosID'
    ];

    // Relaciones directas para reportes rápidos de seguridad
    public function cargo() { return $this->belongsTo(Cargo::class, 'CargoID'); }
    public function permiso() { return $this->belongsTo(Permiso::class, 'PermisosID'); }
}