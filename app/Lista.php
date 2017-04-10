<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Lista extends Model
{
	protected $table ='lista';
	protected $primaryKey = 'idLista';
	
	protected $fillable = ['codigoLista', 'nombreLista'];

	public $timestamps = false;

		public function sublistas() 
	{
		return $this->hasMany('App\SubLista','Lista_idLista');
	}

		public function documentopropiedad() 
	{
		return $this->hasOne('App\SistemaInformacion','idSistemaInformacion');
	}
}