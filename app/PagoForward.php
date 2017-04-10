<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PagoForward extends Model
{
	protected $table ='pagoforward';
	protected $primaryKey = 'idPagoForward';
	
	protected $fillable = ['fechaPagoForward','Forward_idForward'];

	public $timestamps = false;	

	public function pagoforwarddetalle() 
	{
		return $this->hasMany('App\PagoForwardDetalle','PagoForward_idPagoForward');
	}
}