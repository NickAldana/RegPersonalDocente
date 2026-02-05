<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Centroformacion extends Model
{
    use HasFactory;

    protected $table = 'Centroformacion';
    protected $primaryKey = 'CentroformacionID';
    public $timestamps = false;

    protected $fillable = ['Nombrecentro', 'Direccion', 'Pais'];
}