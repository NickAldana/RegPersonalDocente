<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gradoacademico extends Model
{
    use HasFactory;

    protected $table = 'Gradoacademico';
    protected $primaryKey = 'GradoacademicoID';
    public $timestamps = false;

    protected $fillable = ['Nombregrado']; // Limpio, sin códigos

    // Relación con Personal (Para saber cuántos Doctores hay)
    public function personal()
    {
        return $this->hasMany(Personal::class, 'GradoacademicoID');
    }
}