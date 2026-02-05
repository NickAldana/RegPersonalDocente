<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cache extends Model
{
    protected $table = 'Cache';
    protected $primaryKey = 'CacheID';
    public $timestamps = false;
    public $incrementing = false; // El ID es string (nvarchar)

    protected $fillable = ['Key', 'Value', 'Expiracion'];
}