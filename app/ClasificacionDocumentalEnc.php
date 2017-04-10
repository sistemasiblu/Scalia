<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ClasificacionDocumentalEnc extends Model
{
    protected $table ='clasificaciondocumentalenc';
    protected $primaryKey = 'idClasificacionDocumentalEnc';
    
    protected $fillable = ['idClasificacionDocumentalEnc'];

    public $timestamps = false; 

    public function clasificaciondocumentales() 
    {
        return $this->hasMany('App\ClasificacionDocumental','ClasificacionDocumentalEnc_idClasificacionDocumentalEnc');
    }
}