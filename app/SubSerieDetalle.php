<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SubSerieDetalle extends Model
{
	protected $table ='subseriedetalle';
	protected $primaryKey = 'idSubSerieDetalle';
	
	protected $fillable = ['SubSerie_idSubSerie', 'Documento_idDocumento'];

	public $timestamps = false;	

	public function subserie()
    {
    	return $this->hasOne('App\SubSerie','idSubSerie');
    }

    public function documento()
	{
		return $this->hasOne('App\Documento','idDocumento');
	}
}