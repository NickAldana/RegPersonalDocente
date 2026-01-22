<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    // 1. Configuración de Tabla y Llave Primaria
    protected $table = 'Users';
    protected $primaryKey = 'IdUser';
    public $timestamps = true; // Usamos CreatedAt y UpdatedAt

    // 2. Mapeo de Nombres de Columnas (Laravel -> SQL Server)
    const CREATED_AT = 'CreatedAt';
    const UPDATED_AT = 'UpdatedAt';

    // 3. Campos asignables masivamente
    protected $fillable = [
        'IdPersonal',
        'Email',
        'Password',
        'Activo',
        'RememberToken', // <--- Importante agregarlo aquí
    ];

    // 4. Campos ocultos (no se envían en respuestas JSON)
    protected $hidden = [
        'Password',
        'RememberToken',
    ];

    // 5. Casting de atributos
    protected function casts(): array
    {
        return [
            'Password' => 'hashed',
            'Activo' => 'boolean',
            'CreatedAt' => 'datetime',
            'UpdatedAt' => 'datetime',
        ];
    }

    // --------------------------------------------------------------------
    // SOLUCIÓN AL ERROR DE LOGOUT (SQLSTATE[42S22])
    // --------------------------------------------------------------------
    // Sobrescribimos el nombre de la columna para que Laravel sepa
    // que en la BD se llama 'RememberToken' y no 'remember_token'.
    public function getRememberTokenName()
    {
        return 'RememberToken';
    }

    // Sobrescribimos el nombre del campo password si Laravel lo busca como 'password' (minúscula)
    public function getAuthPasswordName()
    {
        return 'Password';
    }

    // --------------------------------------------------------------------
    // RELACIONES
    // --------------------------------------------------------------------
    
    public function personal()
    {
        return $this->belongsTo(Personal::class, 'IdPersonal', 'IdPersonal');
    }

    // Helper para verificar permisos (Permisos)
    // Helper para verificar permisos (Permisos)
    public function canDo($permisoNombre)
    {
        // 1. Si no tiene personal o cargo, fuera.
        if (!$this->personal || !$this->personal->cargo) {
            return false;
        }

        // 2. Optimización: Cachear permisos en memoria para no consultar BD mil veces
        // Laravel carga la relación 'cargo.permisos' automáticamente si usamos 'with' o lazy loading
        
        // Obtenemos la colección de permisos del cargo
        $permisosDelCargo = $this->personal->cargo->permisos; 

        // 3. Verificamos si la colección contiene el nombre del permiso buscado
        return $permisosDelCargo->contains('NombrePermiso', $permisoNombre);
    }
    
    // Método auxiliar para obtener el cargo
    public function cargo()
    {
        return $this->personal ? $this->personal->cargo : null;
    }
}