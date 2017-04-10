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
}
