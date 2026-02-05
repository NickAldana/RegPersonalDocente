<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tipopublicacion extends Model
{
    use HasFactory;

    protected $table = 'Tipopublicacion';
    protected $primaryKey = 'TipopublicacionID';
    public $timestamps = false;

    protected $fillable = ['Nombretipo', 'Descripcion', 'Pais'];
}