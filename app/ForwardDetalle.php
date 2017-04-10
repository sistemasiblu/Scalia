<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ForwardDetalle extends Model
{
	protected $table ='forwarddetalle';
	protected $primaryKey = 'idForwardDetalle';
	
	protected $fillable = ['Forward_idForward', 'Temporada_idTemporada', 'nombreTemporadaForwardDetalle', 'Compra_idCompra', 'numeroCompraForwardDetalle', 'valorForwardDetalle', 'valorRealForwardDetalle'];

	public $timestamps = false;	

	public function forward() 
	{
		return $this->hasOne('App\Forward','idForward');
	}
}