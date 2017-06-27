<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AgendaPermisoDetalle extends Model
{
	protected $table ='agendapermisodetalle';
	protected $primaryKey = 'idAgendaPermisoDetalle';
	
	protected $fillable = ['AgendaPermiso_idAgendaPermiso', 'Users_idPropietario', 'CategoriaAgenda_idCategoriaAgenda', 'adicionarAgendaPermisoDetalle', 'modificarAgendaPermisoDetalle', 'eliminarAgendaPermisoDetalle', 'consultarAgendaPermisoDetalle'];

	public $timestamps = false;	
}