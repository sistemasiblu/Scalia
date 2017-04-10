<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DocumentoCRMCampo extends Model
{
    protected $table = 'documentocrmcampo';
    protected $primaryKey = 'idDocumentoCRMCampo';

    protected $fillable = ['DocumentoCRM_idDocumentoCRM', 'CampoCRM_idCampoCRM', 
    'mostrarGridDocumentoCRMCampo', 'mostrarVistaDocumentoCRMCampo', 'obligatorioDocumentoCRMCampo',
    'solicitanteDocumentoCRMCampo','asesorDocumentoCRMCampo','aprobadorDocumentoCRMCampo'];

    public $timestamps = false;

    public function documentocrm()
    {
    	return $this->hasOne('App\DocumentoCRM','DocumentoCRM_idDocumentoCRM');
    }

}