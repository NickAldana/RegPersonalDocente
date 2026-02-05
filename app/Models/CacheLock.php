<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CacheLock extends Model
{
    protected $table = 'Cache_locks'; // Nombre exacto del SQL
    protected $primaryKey = 'Cache_locksID';
    public $timestamps = false;
    public $incrementing = false;

    protected $fillable = ['Key', 'Owner', 'Expiracion'];
}