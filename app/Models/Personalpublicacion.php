<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class Personalpublicacion extends Pivot
{
    protected $table = 'Personalpublicacion';
    protected $primaryKey = 'PersonalpublicacionID';
    public $timestamps = false;
    public $incrementing = true;

    protected $fillable = [
        'RolID', // FK a la tabla Rol
        'PersonalID',
        'PublicacionID'
    ];

    // ========================================================================
    // RELACIONES TRIPLES
    // ========================================================================

    public function personal() { return $this->belongsTo(Personal::class, 'PersonalID'); }
    public function publicacion() { return $this->belongsTo(Publicacion::class, 'PublicacionID'); }
    
    /**
     * Relación clave para saber si fue Autor o Co-autor.
     */
    public function rol()
    {
        return $this->belongsTo(Rol::class, 'RolID', 'RolID');
    }
}