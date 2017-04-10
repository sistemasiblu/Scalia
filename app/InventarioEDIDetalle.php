<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
class InventarioEDIDetalle extends Model
{
    protected $table = 'inventarioedidetalle';
    protected $primaryKey = 'idInventarioEDIDetalle';
    protected $fillable = ['InventarioEDI_idInventarioEDI', 'eanProductoInventarioEDI', 'cantidadInventarioEDIDetalle', 'precio1InventarioEDIDetalle', 'precio2InventarioEDIDetalle', 'eanAlmacenInventarioEDIDetalle'];

    public $timestamps = false;
    
    public function inventarioedi()
    {
        return $this->hasOne('App\InventarioEDI','idInventarioEDI');
    }
    
}