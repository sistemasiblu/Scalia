<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DocumentoCRMGrafico extends Model
{
    protected $table = 'documentocrmgrafico';
    protected $primaryKey = 'idDocumentoCRMGrafico';

    protected $fillable = ['idDocumentoCRMGrafico', 'DocumentoCRM_idDocumentoCRM', 'tituloDocumentoCRMGrafico', 'tipoDocumentoCRMGrafico', 'valorDocumentoCRMGrafico', 'serieDocumentoCRMGrafico', 'filtroDocumentoCRMGrafico'];

    public $timestamps = false;

    public function documentocrm()
    {
    	return $this->hasOne('App\DocumentoCRM','DocumentoCRM_idDocumentoCRM');
    }

}