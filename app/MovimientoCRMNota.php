<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MovimientoCRMNota extends Model
{
    protected $table = 'movimientocrmnota';
    protected $primaryKey = 'idMovimientoCRMNota';

    protected $fillable = 
    [
	 	'MovimientoCRM_idMovimientoCRM', 'Users_idUsuario', 'fechaMovimientoCRMNota','observacionMovimientoCRMNota'
    ];

    public $timestamps = false;

}
