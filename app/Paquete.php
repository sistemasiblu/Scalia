<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Paquete extends Model
{
    protected $table = 'paquete';
    protected $primaryKey = 'idPaquete';

    protected $fillable = ['ordenPaquete', 'nombrePaquete', 'iconoPaquete'];

    public $timestamps = false;
}
