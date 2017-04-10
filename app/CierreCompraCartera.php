<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CierreCompraCartera extends Model
{
	protected $table ='cierrecompracartera';
	protected $primaryKey = 'idCierreCompraCartera';
	
	protected $fillable = ['Documento_idDocumento', 'Movimiento_idMovimiento', 'valorCierreCompraCartera', 'CierreCompra_idCierreCompra'];

	public $timestamps = false;	

	public function cierrecompra()
    {
    	return $this->hasOne('App\CierreCompra','idCierreCompra');
    }
}