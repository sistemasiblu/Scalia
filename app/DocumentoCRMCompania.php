<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DocumentoCRMCompania extends Model
{
    protected $table = 'documentocrmcompania';
    protected $primaryKey = 'idDocumentoCRMCompania';

    protected $fillable = [ 'DocumentoCRM_idDocumentoCRM', 'Compania_idCompania'];

    public $timestamps = false;

    public function documentocrm()
    {
    	return $this->hasOne('App\DocumentoCRM','DocumentoCRM_idDocumentoCRM');
    }

}