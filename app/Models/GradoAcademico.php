<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GradoAcademico extends Model
{
    use HasFactory;

    protected $table = 'GradoAcademico';
    protected $primaryKey = 'IdGradoAcademico';
    public $timestamps = false;

    protected $fillable = [
        'NombreGrado'
    ];
    public function formaciones()
{
    return $this->hasMany(Formacion::class, 'IdGradoAcademico', 'IdGradoAcademico');
}

}