<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Publicaciones extends Model
{
    use HasFactory;

    protected $table = 'Publicaciones';
    protected $primaryKey = 'IdPublicacion';
    public $timestamps = false;

    protected $fillable = [
        'IdPersonal',
        'IdTipoPublicacion',
        'FechaPublicacion',
        'NombrePublicacion'
    ];
    
    // Convertir la fecha a instancia de Carbon automáticamente
    protected $casts = [
        'FechaPublicacion' => 'date',
    ];

    public function personal()
    {
        return $this->belongsTo(Personal::class, 'IdPersonal', 'IdPersonal');
    }
}