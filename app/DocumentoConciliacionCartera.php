<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DocumentoConciliacionCartera extends Model
{
    protected $table = 'documentoconciliacioncartera';
    protected $primaryKey = 'idDocumentoConciliacionCartera';

    protected $fillable = ['DocumentoConciliacion_idDocumentoConciliacion', 'ValorConciliacion_idValorConciliacion', 'cuentasLocalDocumentoConciliacionCartera', 
    						'cuentasNiifDocumentoConciliacionCartera'];
    						
    public $timestamps = false;

    function DocumentoConciliacion()
    {
    	return $this->hasOne('App\DocumentoConciliacion','idDocumentoConciliacion');
    }
}