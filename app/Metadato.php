<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Metadato extends Model
{
    protected $table ='metadato';
    protected $primaryKey = 'idMetadato';
    
    protected $fillable = ['tituloMetadato','tipoMetadato','Lista_idLista','opcionMetadato','longitudMetadato', 'valorBaseMetadato'];

    public $timestamps = false; 
}