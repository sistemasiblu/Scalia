<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrigenCRM extends Model
{
    protected $table = 'origencrm';
    protected $primaryKey = 'idOrigenCRM';

    protected $fillable = ['codigoOrigenCRM', 'nombreOrigenCRM','GrupoEstado_idGrupoEstado'];

    public $timestamps = false;
}
