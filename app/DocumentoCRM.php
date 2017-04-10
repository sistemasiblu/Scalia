<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DocumentoCRM extends Model
{
    protected $table = 'documentocrm';
    protected $primaryKey = 'idDocumentoCRM';

    protected $fillable = ['codigoDocumentoCRM', 'nombreDocumentoCRM', 'tipoDocumentoCRM', 'numeracionDocumentoCRM', 'longitudDocumentoCRM', 'desdeDocumentoCRM', 'hastaDocumentoCRM', 'actualDocumentoCRM', 'GrupoEstado_idGrupoEstado', 'Compania_idCompania'];

    public $timestamps = false;

    public function documentocrmcampo()
    {
    	return $this->hasMany('App\DocumentoCRMCampo','DocumentoCRM_idDocumentoCRM');
    }

    public function documentocrmgrafico()
    {
        return $this->hasMany('App\DocumentoCRMGrafico','DocumentoCRM_idDocumentoCRM');
    }

    public function documentocrmcompania()
    {
    	return $this->hasMany('App\DocumentoCRMCompania','DocumentoCRM_idDocumentoCRM');
    }

    public function documentocrmrol()
    {
        return $this->hasMany('App\DocumentoCRMRol','DocumentoCRM_idDocumentoCRM');
    }

    public function presupuesto()
    {
        return $this->hasMany('App\Presupuesto','DocumentoCRM_idDocumentoCRM');
    }

}