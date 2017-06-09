<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DocumentoConciliacion extends Model
{
    protected $table = 'documentoconciliacion';
    protected $primaryKey = 'idDocumentoConciliacion';

    protected $fillable = ['Documento_idDocumento', 'Compania_idCompania'];

    public $timestamps = false;

    function DocumentoConciliacionComercial()
    {
    	return $this->hasMany('App\DocumentoConciliacionComercial','DocumentoConciliacion_idDocumentoConciliacion');
    }

    function DocumentoConciliacionCartera()
    {
    	return $this->hasMany('App\DocumentoConciliacionCartera','DocumentoConciliacion_idDocumentoConciliacion');
    }

    function DocumentoConciliacionCredito()
    {
    	return $this->hasMany('App\DocumentoConciliacionCredito','DocumentoConciliacion_idDocumentoConciliacion');
    }
}
