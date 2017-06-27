<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AgendaPermiso extends Model
{
	protected $table ='agendapermiso';
	protected $primaryKey = 'idAgendaPermiso';
	
	protected $fillable = ['Users_idAutorizado'];

	public $timestamps = false;	
}