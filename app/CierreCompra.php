<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CierreCompra extends Model
{
	protected $table ='cierrecompra';
	protected $primaryKey = 'idCierreCompra';
	
	protected $fillable = ['numeroCierreCompra', 'fechaCierreCompra', 'descripcionCierreCompra', 'Tercero_idProveedor', 'Users_id'];

	public $timestamps = false;	

	public function cierrecompracartera() 
	{
		return $this->hasMany('App\CierreCompraCartera','CierreCompra_idCierreCompra');
	}

	public function cierrecomprasaldo() 
	{
		return $this->hasMany('App\CierreCompraSaldo','CierreCompra_idCierreCompra');
	}
}