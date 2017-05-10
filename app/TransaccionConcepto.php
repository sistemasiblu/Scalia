<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TransaccionConcepto extends Model
{
     protected $table='transaccionconcepto';
    protected $primaryKey='idTransaccionConcepto';
    protected $fillable=['TransaccionActivo_idTransaccionActivo', 'ConceptoActivo_idConceptoActivo'];
    public $timestamps=false;
}
