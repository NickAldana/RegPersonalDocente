<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sessione extends Model
{
    protected $table = 'Sessiones'; // Tu tabla personalizada
    protected $primaryKey = 'SessionesID';
    public $timestamps = false;
    public $incrementing = false; // Es string

    protected $fillable = [
        'User_id', 'Ip_address', 'User_agent', 'Payload', 'Last_activity'
    ];

    // Relación opcional para ver quién es el usuario de la sesión
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'User_id', 'UsuarioID');
    }
}