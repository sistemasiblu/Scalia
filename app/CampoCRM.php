<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CampoCRM extends Model
{
    protected $table='campocrm';
    protected $primaryKey = 'idCampoCRM';
    protected $fillable = 
    [
      'tipoCampoCRM', 'nombreCampoCRM', 'descripcionCampoCRM', 'relacionTablaCampoCRM', 'relacionIdCampoCRM', 'relacionNombreCampoCRM', 'relacionAliasCampoCRM'
    ];
    public $timestamps = false;

}
