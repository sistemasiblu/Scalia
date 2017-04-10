<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SectorEmpresa extends Model
{
    protected $table = 'sectorempresa';
    protected $primaryKey = 'idSectorEmpresa';

    protected $fillable = ['codigoSectorEmpresa', 'nombreSectorEmpresa','Compania_idCompania'];

    public $timestamps = false;
}
