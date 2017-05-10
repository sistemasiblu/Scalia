<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TransaccionRol extends Model
{
    protected $table='transaccionrol';
    protected $primaryKey='idTransaccionRol';
    protected $fillable=['TransaccionActivo_idTransaccionActivo', 'Rol_idRol', 'adicionarTransaccionRol', 'modificarTransaccionRol', 'anularTransaccionRol', 'consultarTransaccionRol', 'autorizarTransaccionRol'];
    public $timestamps=false;
}
