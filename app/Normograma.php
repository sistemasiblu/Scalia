<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Normograma extends Model
{
	protected $table ='normograma';
	protected $primaryKey = 'idNormograma';
	
	protected $fillable = ['nombreNormograma', 'descripcionNormograma', 'derogada_vigenteNormograma'];

	public $timestamps = false;
}
	