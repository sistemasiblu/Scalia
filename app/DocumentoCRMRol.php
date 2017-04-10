<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DocumentoCRMRol extends Model
{
    protected $table = 'documentocrmrol';
    protected $primaryKey = 'idDocumentoCRMRol';

    protected $fillable = [ 'DocumentoCRM_idDocumentoCRM', 'Rol_idRol',
     'adicionarDocumentoCRMRol', 'modificarDocumentoCRMRol',
     'anularDocumentoCRMRol', 'consultarDocumentoCRMRol',
     'aprobarDocumentoCRMRol'];

    public $timestamps = false;

    public function documentocrm()
    {
    	return $this->hasOne('App\DocumentoCRM','DocumentoCRM_idDocumentoCRM');
    }

}