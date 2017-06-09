<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FrecuenciaMedicion extends Model
{
    protected $table='frecuenciamedicion';
    protected $primaryKey='idFrecuenciaMedicion';
    protected $fillable=['codigoFrecuenciaMedicion', 'nombreFrecuenciaMedicion', 'valorFrecuenciaMedicion', 'unidadFrecuenciaMedicion'];
    public $timestamps=false;
}
