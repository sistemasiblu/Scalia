<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MovimientoActivoDetalle extends Model
{
    protected $table='movimientoactivodetalle';
    protected $primaryKey='idMovimientoActivoDetalle';
    protected $fillable=['MovimientoActivo_idMovimientoActivo', 'Localizacion_idOrigen', 'Localizacion_idDestino', 'Activo_idActivo', 'cantidadMovimientoActivoDetalle', 'observacionMovimientoActivoDetalle','MovimientoActivo_idDocumentoInterno','estadoMovimientoActivoDetalle','RechazoActivo_idRechazoActivo'];

    public $timestamps=false;
}
