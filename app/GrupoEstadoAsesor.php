<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GrupoEstadoAsesor extends Model
{
    protected $table = 'grupoestadoasesor';
    protected $primaryKey = 'idGrupoEstadoAsesor';
    protected $fillable = ['GrupoEstado_idGrupoEstado','Tercero_idAsesor'];
    public $timestamps = false;


    
}
