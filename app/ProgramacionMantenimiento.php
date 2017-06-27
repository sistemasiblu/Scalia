<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProgramacionMantenimiento extends Model
{
    protected $table ='programacionmantenimiento';
	protected $primaryKey = 'idProgramacionMantenimiento';
	
	protected $fillable = ['ProtocoloMantenimiento_idProtocoloMantenimiento', 'TipoActivo_idTipoActivo', 'TipoAccion_idTipoAccion', 'Dependencia_idDependencia', 'fechaInicialProgramacionMantenimiento', 'fechaMaximaProgramacionMantenimiento', 'Compania_idCompania'];

	public $timestamps = false;	
}
