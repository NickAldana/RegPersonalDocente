<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Usuario extends Authenticatable
{
    use Notifiable;

    // 1. Configuración de Tabla (SQL Server V3.1)
    protected $table = 'Usuario';
    protected $primaryKey = 'UsuarioID'; // PK Identity
    public $timestamps = false; // No usas created_at/updated_at estándar

    // 2. Campos Asignables
    protected $fillable = [
        'NombreUsuario',      // Login del sistema (ej: 'jperez')
        'Correo',             // Email único
        'Contraseña',         // Hash
        'RecordatorioToken',  // Token de "Recuérdame"
        'Activo',             // 1 o 0
        'Idpersonal'          // Referencia cruzada (opcional, pero está en tu tabla)
    ];

    // 3. Seguridad
    protected $hidden = [
        'Contraseña',
        'RecordatorioToken',
    ];

    protected $casts = [
        'Activo' => 'boolean',
        'Creacionfecha' => 'datetime',
    ];

    // ========================================================================
    // CONFIGURACIÓN DE AUTH (PUENTE LARAVEL <-> SQL ESPAÑOL)
    // ========================================================================

    // Laravel busca 'password', le damos 'Contraseña'
    public function getAuthPassword()
    {
        return $this->Contraseña;
    }

    // Laravel busca 'remember_token', le damos 'RecordatorioToken'
    public function getRememberTokenName()
    {
        return 'RecordatorioToken';
    }

    // ========================================================================
    // RELACIONES
    // ========================================================================

    /**
     * Un Usuario tiene asociado UN perfil de Personal.
     * La FK está en la tabla 'Personal' (UsuarioID).
     */
    public function personal()
    {
        return $this->hasOne(Personal::class, 'UsuarioID', 'UsuarioID');
    }

    // ========================================================================
    // LÓGICA DE PERMISOS (RBAC) - TU FUNCIÓN 'canDo' ACTUALIZADA
    // ========================================================================

    /**
     * Verifica si el usuario tiene permiso para una acción.
     * Ruta: Usuario -> Personal -> Cargo -> Permisos
     * Uso: if ($user->canDo('crear.docente')) { ... }
     */
    public function canDo($nombrePermiso)
    {
        // 1. Validaciones básicas: Debe estar activo y tener perfil asociado
        if (!$this->Activo || !$this->personal || !$this->personal->cargo) {
            return false;
        }

        // 2. Obtener los permisos del cargo (Laravel cachea esto si usas 'with')
        $permisos = $this->personal->cargo->permisos;

        // 3. Buscar si existe el permiso en la colección
        // Nota: Usamos 'Nombrepermiso' tal cual está en el modelo Permiso
        return $permisos->contains('Nombrepermiso', $nombrePermiso);
    }
}