<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permiso extends Model
{
    protected $table = 'Permisos';
    protected $primaryKey = 'IdPermiso';
    public $timestamps = false; // Tu tabla no tiene created_at/updated_at

    protected $fillable = ['NombrePermiso', 'Descripcion'];

    // Relación inversa (opcional, pero buena práctica)
    public function cargos()
    {
        return $this->belongsToMany(Cargo::class, 'CargoPermiso', 'IdPermiso', 'IdCargo');
    }
}