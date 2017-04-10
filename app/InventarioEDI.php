<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
class InventarioEDI extends Model
{
    protected $table = 'inventarioedi';
    protected $primaryKey = 'idInventarioEDI';
    protected $fillable = ['numeroInventarioEDI','Tercero_idCliente', 'nombreClienteInventarioEDI', 'fechaInicialInventarioEDI', 'fechaFinalInventarioEDI', 'Compania_idCompania'];

    public $timestamps = false;
    
    public function inventarioedidetalle()
    {
        return $this->hasMany('App\InventarioEDIDetalle','InventarioEDI_idInventarioEDI');
    }
    
}