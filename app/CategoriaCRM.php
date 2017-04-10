<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CategoriaCRM extends Model
{
    protected $table = 'categoriacrm';
    protected $primaryKey = 'idCategoriaCRM';

    protected $fillable = ['codigoCategoriaCRM', 'nombreCategoriaCRM','GrupoEstado_idGrupoEstado'];

    public $timestamps = false;
}
