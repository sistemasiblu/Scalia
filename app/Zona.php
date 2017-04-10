<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Zona extends Model
{
    protected $table = 'zona';
    protected $primaryKey = 'idZona';

    protected $fillable = ['codigoZona', 'nombreZona', 'Compania_idCompania'];

    public $timestamps = false;
}
