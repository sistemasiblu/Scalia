<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SitioWeb extends Model
{
	protected $table ='sitioweb';
	protected $primaryKey = 'idSitioWeb';
	
	protected $fillable = ['descripcionSitioWeb', 'urlSitioWeb'];

	public $timestamps = false;	
}