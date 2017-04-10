<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Forward extends Model
{
	protected $table ='forward';
	protected $primaryKey = 'idForward';
	
	protected $fillable = ['numeroForward', 'descripcionForward', 'fechaNegociacionForward', 'fechaVencimientoForward', 'modalidadForward', 'valorDolarForward', 'tasaForward', 'tasaInicialForward', 'valorPesosForward','bancoForward', 'Tercero_idBanco', 'rangeForward', 'devaluacionForward', 'spotForward', 'estadoForward', 'ForwardPadre_idForwardPadre'];

	public $timestamps = false;	

	public function forwarddetalle() 
	{
		return $this->hasMany('App\ForwardDetalle','Forward_idForward');
	}
}