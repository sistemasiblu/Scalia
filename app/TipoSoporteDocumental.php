<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TipoSoporteDocumental extends Model
{
	protected $table ='tiposoportedocumental';
	protected $primaryKey = 'idTipoSoporteDocumental';
	
	protected $fillable = ['codigoTipoSoporteDocumental','nombreTipoSoporteDocumental'];

	public $timestamps = false;
}
	