<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
class VentaEDI extends Model
{
    protected $table = 'ventaedi';
    protected $primaryKey = 'idVentaEDI';
    protected $fillable = ['numeroVentaEDI','Tercero_idCliente', 'nombreClienteVentaEDI', 'fechaInicialVentaEDI', 'fechaFinalVentaEDI', 'Compania_idCompania'];

    public $timestamps = false;
    
    public function ventaedidetalle()
    {
        return $this->hasMany('App\VentaEDIDetalle','VentaEDI_idVentaEDI');
    }
    
}