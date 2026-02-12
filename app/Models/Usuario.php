<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Usuario extends Authenticatable
{
    use Notifiable;

    /**
     * CONFIGURACIÓN DE TABLA
     * Mapeo exacto a la base de datos dbpersonalacreditacion.
     */
    protected $table = 'usuario'; //
    protected $primaryKey = 'UsuarioID'; //
    public $timestamps = false; //

    /**
     * IMPORTANTE: Laravel 12 intenta actualizar contraseñas automáticamente (rehash).
     * Desactivamos esto para evitar que busque la columna 'password' (en minúscula) que no existe.
     */
    protected $rehashPasswordIfRequired = false; //

    /**
     * ATRIBUTOS ASIGNABLES
     */
    protected $fillable = [
        'NombreUsuario',
        'Correo',
        'Password', // Tu nueva columna
        'RecordatorioToken',
        'Activo',
        'Idpersonal'
    ];

    /**
     * ATRIBUTOS OCULTOS (Seguridad)
     */
    protected $hidden = [
        'Password',
        'RecordatorioToken',
    ];

    /**
     * CASTING DE DATOS
     */
    protected $casts = [
        'Activo' => 'boolean',
        'Creacionfecha' => 'datetime',
        'Finalfecha' => 'datetime',
    ];

    // ========================================================================
    // MAPEADO PARA EL SISTEMA DE AUTENTICACIÓN
    // ========================================================================

    /**
     * Como tu columna empieza con Mayúscula (Password), Laravel necesita este método 
     * para saber dónde encontrar el hash durante el login.
     */
    public function getAuthPassword()
    {
        return $this->Password; //
    }

    /**
     * Mapeo del token de "recordarme" para tu tabla personalizada.
     */
    public function getRememberTokenName()
    {
        return 'RecordatorioToken'; //
    }

    // ========================================================================
    // RELACIONES (Eloquents)
    // ========================================================================

   // USAR ESTO SI YA LLENASTE EL CAMPO Idpersonal EN LA TABLA USUARIO
public function personal()
{
    // belongsTo(Modelo, ClaveForaneaLocal, ClavePrimariaOtroModelo)
    return $this->belongsTo(Personal::class, 'Idpersonal', 'PersonalID');
}

    public function bitacoras(): HasMany
    {
        return $this->hasMany(Bitacora::class, 'UsuarioID', 'UsuarioID');
    }

    // ========================================================================
    // LÓGICA DE CONTROL DE ACCESO (RBAC)
    // ========================================================================

    /**
     * Verifica permisos y gestiona el Acceso Total para administradores como Ernesto.
     */
    public function canDo(string $nombrePermiso): bool
    {
        if (!$this->Activo || !$this->personal || !$this->personal->cargo) {
            return false;
        }

        $permisos = $this->personal->cargo->permisos;

        // Llave maestra para el Director de Acreditación
        if ($permisos->contains('Nombrepermiso', 'acceso_total')) {
            return true;
        }

        return $permisos->contains('Nombrepermiso', $nombrePermiso);
    }
}