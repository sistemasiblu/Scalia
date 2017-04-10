<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EventoCRM extends Model
{
    protected $table = 'eventocrm';
    protected $primaryKey = 'idEventoCRM';

    protected $fillable = ['codigoEventoCRM', 'nombreEventoCRM','GrupoEstado_idGrupoEstado'];

    public $timestamps = false;
}
