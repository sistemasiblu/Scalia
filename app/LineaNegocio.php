<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LineaNegocio extends Model
{
    protected $table = 'lineanegocio';
    protected $primaryKey = 'idLineaNegocio';

    protected $fillable = ['codigoLineaNegocio', 'nombreLineaNegocio', 'Compania_idCompania'];

    public $timestamps = false;

    function presupuesto()
    {
		return $this->hasMany('App\Presupuesto','LineaNegocio_idLineaNegocio');
    }
}
