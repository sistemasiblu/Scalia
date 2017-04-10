<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CorreoEmbarque extends Model
{
    protected $table ='correoembarque';
    protected $primaryKey = 'idCorreoEmbarque';
    
    protected $fillable = ['tipoCorreoEmbarque','destinatarioCorreoEmbarque','copiaCorreoEmbarque','asuntoCorreoEmbarque','mensajeCorreoEmbarque'];

    public $timestamps = false; 
}