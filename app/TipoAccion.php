<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TipoAccion extends Model
{
    protected $table= 'tipoaccion';
    protected $primaryKey= 'idTipoAccion';
    protected $fillable= ['codigoTipoAccion','nombreTipoAccion', 'observacionTipoAccion'];
    public $timestamps=false;



}



