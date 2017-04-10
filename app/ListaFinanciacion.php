<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ListaFinanciacion extends Model
{
	protected $table ='listafinanciacion';
	protected $primaryKey = 'idListaFinanciacion';
	
	protected $fillable = ['codigoListaFinanciacion', 'nombreListaFinanciacion', 'codigoSayaListaFinanciacion', 'tipoListaFinanciacion'];

	public $timestamps = false;
}
	