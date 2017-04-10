<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ValorConciliacion extends Model
{
    protected $table = 'valorconciliacion';
    protected $primaryKey = 'idValorConciliacion';

    protected $fillable = ['moduloValorConciliacion', 'nombreValorConciliacion', 'campoValorConciliacion'];

    public $timestamps = false;

    function DocumentoConciliacionComercial()
    {
    	return $this->hasMany('App\DocumentoConciliacionComercial','DocumentoConciliacion_idDocumentoConciliacion');
    }
}
