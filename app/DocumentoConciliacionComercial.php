<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DocumentoConciliacionComercial extends Model
{
    protected $table = 'documentoconciliacioncomercial';
    protected $primaryKey = 'idDocumentoConciliacionComercial';

    protected $fillable = ['DocumentoConciliacion_idDocumentoConciliacion', 'ValorConciliacion_idValorConciliacion', 'cuentasLocalDocumentoConciliacionComercial', 
    						'cuentasNiifDocumentoConciliacionComercial'];
    						
    public $timestamps = false;

    function DocumentoConciliacion()
    {
    	return $this->hasOne('App\DocumentoConciliacion','idDocumentoConciliacion');
    }
}