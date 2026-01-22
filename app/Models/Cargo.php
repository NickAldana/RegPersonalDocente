<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cargo extends Model
{
    use HasFactory;

    protected $table = 'Cargo';
    protected $primaryKey = 'IdCargo';
    public $timestamps = false;

    protected $fillable = ['NombreCargo'];

    // 1. Relación con Personal (Un cargo tiene muchas personas)
    public function personal()
    {
        return $this->hasMany(Personal::class, 'IdCargo', 'IdCargo');
    }

    // 2. RELACIÓN DE SEGURIDAD (Muchos a Muchos)
    // Esto permite hacer: $cargo->permisos
    public function permisos()
    {
        return $this->belongsToMany(
            Permiso::class,      // El modelo final
            'CargoPermiso',      // La tabla intermedia en SQL
            'IdCargo',           // La llave foránea de este modelo en la pivote
            'IdPermiso'          // La llave foránea del otro modelo en la pivote
        );
    }

    // 3. Helper para Jerarquía (Para evitar que un Jefe edite a un Rector)
    public function getNivelJerarquicoAttribute()
    {
        // Definimos niveles duros basados en el ID o Nombre
        // Rector(1)/Vice(2) > Decano(3) > Jefe(4) > Docente(5)
        // Damos un valor numérico: Mayor número = Menor jerarquía (o al revés, como prefieras)
        
        // Usemos: Nivel Alto = Número Alto (Rector=100, Docente=10)
        switch ($this->IdCargo) {
            case 1: return 100; // Rector
            case 2: return 90;  // Vicerrector
            case 3: return 80;  // Decano
            case 4: return 50;  // Jefe de Carrera
            case 5: return 10;  // Docente
            default: return 0;
        }
    }
}