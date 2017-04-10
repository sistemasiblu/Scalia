<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CierreCompraSaldo extends Model
{
	protected $table ='cierrecomprasaldo';
	protected $primaryKey = 'idCierreCompraSaldo';
	
	protected $fillable = ['Compra_idCompra', 'valorCierreCompraSaldo', 'Forward_idForward', 'CierreCompra_idCierreCompra'];

	public $timestamps = false;	

	public function dependencia()
    {
    	return $this->hasOne('App\CierreCompra','idCierreCompra');
    }
}