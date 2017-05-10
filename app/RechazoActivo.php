<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RechazoActivo extends Model
{
    protected $table='rechazoactivo';
    protected $primaryKey='idRechazoActivo';
    protected $fillable=['codigoRechazoActivo', 'nombreRechazoActivo','observacionRechazoActivo'];
    public $timestamps=false;
}
