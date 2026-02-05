<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mediopublicacion extends Model
{
    use HasFactory;

    protected $table = 'Mediopublicacion';
    protected $primaryKey = 'MediopublicacionID';
    public $timestamps = false;

    protected $fillable = [
        'Nombremedio', 'Url', 'Pais', 'Datoscontacto', 'Correo'
    ];
}