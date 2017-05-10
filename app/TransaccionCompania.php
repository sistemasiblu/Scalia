<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TransaccionCompania extends Model
{
     protected $table='transaccioncompania';
    protected $primaryKey='idTransaccionCompania';
    protected $fillable=['TransaccionActivo_idTransaccionActivo', 'Compania_idCompania'];
    public $timestamps=false;
}
