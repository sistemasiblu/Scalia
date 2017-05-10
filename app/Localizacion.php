<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Localizacion extends Model
{
    protected $table='localizacion';
    protected $primaryKey='idLocalizacion';
    protected $fillable=['codigoLocalizacion', 'nombreLocalizacion', 'Localizacion_idPadre', 'observacionLocalizacion'];
    public $timestamps=false;
}
