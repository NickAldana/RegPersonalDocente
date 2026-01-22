<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CarreraPersonal extends Model
{
    use HasFactory;

    protected $table = 'CarreraPersonal';
    protected $primaryKey = 'IdCarreraPersonal';
    public $timestamps = false;

    protected $fillable = [
        'IdCarrera', 
        'IdPersonal',
        'Gestion' // [NUEVO]
    ];

    public function carrera()
    {
        return $this->belongsTo(Carrera::class, 'IdCarrera', 'IdCarrera');
    }

    public function personal()
    {
        return $this->belongsTo(Personal::class, 'IdPersonal', 'IdPersonal');
    }
}