<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MovimientoCRMArchivo extends Model
{
    protected $table = 'movimientocrmarchivo';
    protected $primaryKey = 'idMovimientoCRMArchivo';

    protected $fillable = ['rutaMovimientoCRMArchivo','MovimientoCRM_idMovimientoCRM'];

    public $timestamps = false;

    
}
