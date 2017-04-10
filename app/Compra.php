<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Compra extends Model
{
    protected $table = 'compra';
    protected $primaryKey = 'idCompra';

    protected $fillable = ['fechaCompra','Temporada_idTemporada', 'nombreTemporadaCompra', 'Tercero_idProveedor', 'nombreProveedorCompra', 'Movimiento_idMovimiento', 'numeroCompra', 'nombreClienteCompra', 'formaPagoClienteCompra', 'eventoCompra', 'compradorVendedorCompra', 'Tercero_idVendedor', 'Tercero_idCliente', 'valorCompra', 'FormaPago_idFormaPago', 'formaPagoProveedorCompra', 'cantidadCompra', 'codigoUnidadMedidaCompra', 'pesoCompra', 'volumenCompra', 'bultoCompra', 'Ciudad_idPuerto', 'nombreCiudadCompra', 'fechaDeliveryCompra', 'fechaForwardCompra', 'valorForwardCompra', 'diaPagoClienteCompra', 'tiempoBodegaCompra', 'fechaMaximaDespachoCompra', 'observacionCompra', 'numeroVersionCompra', 'estadoCompra', 'envioCorreoCompra', 'DocumentoImportacion_idDocumentoImportacion','Usuario_idUsuario'];

    public $timestamps = false;

    public function documentoimportacion()
    {
    	return $this->hasOne('App\DocumentoImportacion','idDocumentoImportacion');
    }

    public function EmbarqueDetalle() 
	{
		return $this->hasMany('App\EmbarqueDetalle','Compra_idCompra');
	}
}
