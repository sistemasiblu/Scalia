<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SubLista extends Model
{
	protected $table ='sublista';
	protected $primaryKey = 'idSubLista';
	
	protected $fillable = ['codigoSubLista', 'nombreSubLista', 'dato1SubLista', 'dato2SubLista', 'dato3SubLista', 'Lista_idLista'];

	public $timestamps = false;	

	public function lista()
	{
		return $this->hasOne('App\Lista','idLista');
	}

}