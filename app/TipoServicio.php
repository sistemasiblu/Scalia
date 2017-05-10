<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TipoServicio extends Model
{
    protected $table= 'tiposervicio';
    protected $primaryKey= 'idTipoServicio';
    protected $fillable= ['codigoTipoServicio','nombreTipoServicio', 'observacionTipoServicio'];
    public $timestamps=false;
}
