<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
class VentaEDIDetalle extends Model
{
    protected $table = 'ventaedidetalle';
    protected $primaryKey = 'idVentaEDIDetalle';
    protected $fillable = ['VentaEDI_idVentaEDI', 'eanProductoVentaEDI', 'cantidadVentaEDIDetalle', 'precio1VentaEDIDetalle', 'precio2VentaEDIDetalle', 'eanAlmacenVentaEDIDetalle'];

    public $timestamps = false;
    
    public function ventaedi()
    {
        return $this->hasOne('App\VentaEDI','idVentaEDI');
    }
    
}