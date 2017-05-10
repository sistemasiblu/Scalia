<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MovimientoActivo extends Model
{
    protected $table='movimientoactivo';
    protected $primaryKey='idMovimientoActivo';
    protected $fillable=['numeroMovimientoActivo', 'fechaElaboracionMovimientoActivo', 'fechaInicioMovimientoActivo', 'fechaFinMovimientoActivo', 'Tercero_idTercero', 'TransaccionActivo_idTransaccionActivo', 'ConceptoActivo_idConceptoActivo', 'documentoInternoMovimientoActivo', 'documentoExternoMovimientoActivo','TransaccionActivo_idDocumentoInterno','estadoMovimientoActivo', 'observacionMovimientoActivo', 'totalUnidadesMovimientoActivo', 'totalArticulosMovimientoActivo', 'Users_idCrea', 'Users_idCambioEstado', 'fechaCambioEstado','Compania_idCompania'];

       public $timestamps=false;
}
