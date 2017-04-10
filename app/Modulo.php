<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Modulo extends Model
{
    protected $table = 'modulo';
    protected $primaryKey = 'idModulo';

    protected $fillable = ['nombreModulo', 'tablaModulo'];

    public $timestamps = false;
}
