<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ConceptoActivo extends Model
{
	protected $table='ConceptoActivo';
    protected $primaryKey='idConceptoActivo';
    protected $fillable=['codigoConceptoActivo', 'nombreConceptoActivo'];
    public $timestamps=false;
}
