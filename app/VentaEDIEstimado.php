<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VentaEDIEstimado extends Model
{
    protected $table = 'ventaediestimado';
    protected $primaryKey = 'idVentaEDIEstimado';
    protected $fillable = ['Producto_idProducto', 'diasVentaEDIEstimado', 'fechaInicioVentaEDIEstimado'];

    public $timestamps = false;    
}
